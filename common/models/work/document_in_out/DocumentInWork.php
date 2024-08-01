<?php

namespace common\models\work\document_in_out;

use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\StringFormatter;
use common\models\scaffold\DocumentIn;
use common\models\work\general\CompanyWork;
use common\models\work\general\FilesWork;
use common\models\work\general\PeopleWork;
use common\models\work\general\PositionWork;
use common\repositories\document_in_out\DocumentInRepository;
use common\repositories\document_in_out\DocumentOutRepository;
use common\repositories\document_in_out\InOutDocumentsRepository;
use common\repositories\general\FilesRepository;
use frontend\events\EventTrait;
use InvalidArgumentException;
use Yii;
use yii\helpers\Url;

/**
 * @property PeopleWork $correspondentWork
 * @property PositionWork $positionWork
 * @property CompanyWork $companyWork
 * @property InOutDocumentsWork $inOutDocumentsWork
 * @property PeopleWork $creatorWork
 * @property PeopleWork $lastUpdateWork
 */
class DocumentInWork extends DocumentIn
{
    use EventTrait;

    /**
     * Имена файлов для сохранения в БД
     */
    public $scanName;
    public $docName;
    public $appName;

    /**
     * Переменные для input-file в форме
     */
    public $scanFile;
    public $docFiles;
    public $appFiles;

    public $needAnswer;
    public $dateAnswer;
    public $nameAnswer;

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'fullNumber' => '№ п/п',
            'localDate' => 'Дата поступления<br>документа',
            'realDate' => 'Дата входящего<br>документа',
            'realNumber' => 'Рег. номер<br>входящего док.',
            'companyName' => 'Наименование<br>корреспондента',
            'documentTheme' => 'Тема документа',
            'sendMethodName' => 'Способ получения',
            'needAnswer' => 'Требуется ответ',
        ]);
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['needAnswer', 'nameAnswer'], 'integer'],
            [['dateAnswer'], 'string'],
            [['scanFile'], 'file', 'skipOnEmpty' => true,
                'extensions' => 'png, jpg, pdf, zip, rar, 7z, tag, txt'],
            [['docFiles'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10,
                'extensions' => 'xls, xlsx, doc, docx, zip, rar, 7z, tag, txt'],
            [['appFiles'], 'file', 'skipOnEmpty' => true,  'maxFiles' => 10,
                'extensions' => 'ppt, pptx, xls, xlsx, pdf, png, jpg, doc, docx, zip, rar, 7z, tag, txt'],
        ]);
    }

    public static function fill($localNumber = 0, $creatorId = null, $app = '', $doc = '', $scan = '')
    {
        $entity = new static();
        $entity->creator_id = $creatorId ?: Yii::$app->user->identity->getId();
        $entity->local_number = $localNumber;
        $entity->app = $app;
        $entity->doc = $doc;
        $entity->scan = $scan;
    }

    public function getFullNumber()
    {
        if ($this->local_postfix == null)
            return $this->local_number;
        else
            return $this->local_number.'/'.$this->local_postfix;
    }

    public function getCompanyName()
    {
        return $this->companyWork->name;
    }

    public function getSendMethodName()
    {
        return Yii::$app->sendMethods->get($this->send_method);
    }

    public function getRealDate()
    {
        return $this->real_date;
    }

    public function getLocalDate()
    {
        return $this->local_date;
    }

    public function getRealNumber()
    {
        return $this->real_number;
    }

    public function getDocumentTheme()
    {
        return $this->document_theme;
    }

    public function getFileLinks($filetype)
    {
        if (!array_key_exists($filetype, FilesHelper::getFileTypes())) {
            throw new InvalidArgumentException('Неизвестный тип файла');
        }

        $files = (Yii::createObject(FilesRepository::class))->get(self::tableName(), $this->id, $filetype);
        $links = [];
        if (count($files) > 0) {
            foreach ($files as $file) {
                /** @var FilesWork $file */
                $links[] = StringFormatter::stringAsLink(
                    FilesHelper::getFilenameFromPath($file->filepath),
                    Url::to(['get-file', 'filepath' => $file->filepath])
                );
            }
        }

        return $links;
    }

    /**
     * Возвращает строку с отображением необходимости ответа на входящий документ
     * @param int $format формат возвращаемого значения (при наличии такой опции) @see StringFormatter
     * @return string
     */
    public function getNeedAnswer(int $format = StringFormatter::FORMAT_RAW)
    {
        if (array_key_exists($format, StringFormatter::getFormats())) {
            $links = (Yii::createObject(InOutDocumentsRepository::class))->get($this->id);

            /** @var InOutDocumentsWork $links */
            if ($links == null) {
                return '';
            }

            if ($links->isDocumentOutEmpty()) {
                if ($links->isNoPeopleTarget()) {
                    if ($links->isNoAnswerDate()) {
                        return 'Требуется ответ';
                    }
                    return 'До '.$links->date;
                }
                return 'До '.$links->date.' от '.$links->responsibleWork->getFIO(PeopleWork::FIO_SURNAME_INITIALS);
            }

            $str = 'Исходящий документ "'.(Yii::createObject(DocumentOutRepository::class))->get($links->document_out_id)->document_theme.'"';
            return $format == StringFormatter::FORMAT_LINK ?
                StringFormatter::stringAsLink($str, Url::to(['document-out/view', 'id' => $links->document_out_id])) : $str;
        }
        throw new \InvalidArgumentException('Неизвестный формат строки');
    }

    public function generateDocumentNumber()
    {
        $repository = Yii::createObject(DocumentInRepository::class);
        $docs = $repository->getAllDocumentsDescDate();
        if (date('Y') !== substr($docs[0]->local_date, 0, 4)) {
            $this->local_number = 1;
        }
        else {
            $docs = $repository->getAllDocumentsInYear();
            if (end($docs)->local_date > $this->local_date && $this->document_theme != 'Резерв') {
                $tempId = 0;
                $tempPre = 0;
                if (count($docs) == 0) {
                    $tempId = 1;
                }
                for ($i = count($docs) - 1; $i >= 0; $i--) {
                    if ($docs[$i]->local_date <= $this->local_date) {
                        $tempId = $docs[$i]->local_number;
                        if ($docs[$i]->local_postfix != null) {
                            $tempPre = $docs[$i]->local_postfix + 1;
                        }
                        else {
                            $tempPre = 1;
                        }
                        break;
                    }
                }

                $this->local_number = $tempId;
                $this->local_postfix = $tempPre;
                Yii::$app->session->addFlash('warning', 'Добавленный документ должен был быть зарегистрирован раньше. Номер документа: '.$this->local_number.'/'.$this->local_postfix);
            }
            else
            {
                if (count($docs) == 0) {
                    $this->local_number = 1;
                }
                else {
                    $this->local_number = end($docs)->local_number + 1;
                }
            }
        }
    }

    // ТОЛЬКО для предварительной обработки полей. Остальные действия - через Event
    public function beforeValidate()
    {
        $this->creator_id = 1/*Yii::$app->user->identity->getId()*/;
        $this->local_date = DateFormatter::format($this->local_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        $this->real_date = DateFormatter::format($this->real_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }

    // --relationships--

    public function getCompanyWork()
    {
        return $this->hasOne(CompanyWork::class, ['id' => 'company_id']);
    }

    public function getPositionWork()
    {
        return $this->hasOne(PositionWork::class, ['id' => 'position_id']);
    }

    public function getInOutDocumentsWork()
    {
        return $this->hasMany(InOutDocumentsWork::class, ['document_in_id' => 'id']);
    }

    public function getCorrespondentWork()
    {
        return $this->hasOne(PeopleWork::class, ['id' => 'correspondent_id']);
    }

    public function getCreatorWork()
    {
        return $this->hasOne(PeopleWork::class, ['id' => 'creator_id']);
    }

    public function getLastUpdateWork()
    {
        return $this->hasOne(PeopleWork::class, ['id' => 'last_update_id']);
    }

    public function setNeedAnswer()
    {
        $this->needAnswer = (Yii::createObject(InOutDocumentsRepository::class))->getByDocumentInId($this->id) ? 1 : 0;
    }
}