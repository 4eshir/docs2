<?php

namespace common\models\work\document_in_out;

use common\helpers\FilesHelper;
use common\helpers\StringFormatter;
use common\models\scaffold\DocumentIn;
use common\models\work\general\CompanyWork;
use common\models\work\general\FilesWork;
use common\models\work\general\PeopleWork;
use common\models\work\general\PositionWork;
use common\repositories\document_in_out\DocumentOutRepository;
use common\repositories\document_in_out\InOutDocumentsRepository;
use common\repositories\general\FilesRepository;
use Yii;
use yii\helpers\Url;
use InvalidArgumentException;

/**
 * @property PeopleWork $correspondentWork
 * @property PositionWork $positionWork
 * @property CompanyWork $companyWork
 */
class DocumentInWork extends DocumentIn
{
    public $scan;
    public $doc;
    public $app;

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
            'needAnswer' => 'Ответ',
        ]);
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['scan', 'doc'], 'required'],
        ]);
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
}