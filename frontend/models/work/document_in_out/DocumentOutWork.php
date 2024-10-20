<?php

namespace frontend\models\work\document_in_out;

use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\StringFormatter;
use common\models\scaffold\DocumentOut;
use common\repositories\document_in_out\DocumentInRepository;
use common\repositories\document_in_out\DocumentOutRepository;
use common\repositories\document_in_out\InOutDocumentsRepository;

use frontend\models\work\dictionaries\CompanyWork;
use frontend\models\work\dictionaries\PositionWork;
use frontend\models\work\general\PeopleWork;
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
class DocumentOutWork extends DocumentOut
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
    public $docFile;
    public $appFile;

    public $isAnswer;
    public $dateAnswer;
    public $nameAnswer;
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'fullNumber' => '№ п/п',
            'documentDate' => 'Дата исходящего<br>документа',
            'sentDate' => 'Дата отправка<br>документа',
            'documentNumber' => 'Рег. номер<br>исходящего док.',
            'companyName' => 'Наименование<br>корреспондента',
            'documentTheme' => 'Тема документа',
            'sendMethodName' => 'Способ получения',
            'isAnswer' => 'Ответ',
        ]);
    }
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['isAnswer', 'nameAnswer'], 'integer'],
            [['dateAnswer'], 'string'],
            [['scanFile'], 'file', 'skipOnEmpty' => true,
                'extensions' => 'png, jpg, pdf, zip, rar, 7z, tag, txt'],
            [['docFile'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10,
                'extensions' => 'xls, xlsx, doc, docx, zip, rar, 7z, tag, txt'],
            [['appFile'], 'file', 'skipOnEmpty' => true,  'maxFiles' => 10,
                'extensions' => 'ppt, pptx, xls, xlsx, pdf, png, jpg, doc, docx, zip, rar, 7z, tag, txt'],
        ]);
    }
    public function getFullNumber()
    {
        if ($this->document_postfix == null)
            return $this->document_number;
        else
            return $this->document_number.'/'.$this->document_postfix;
    }
    public function getAnswer(){
        return $this->is_answer;
    }
    public function getFileLinks($filetype)
    {
        if (!array_key_exists($filetype, FilesHelper::getFileTypes())) {
            throw new InvalidArgumentException('Неизвестный тип файла');
        }

        $addPath = '';
        switch ($filetype) {
            case FilesHelper::TYPE_SCAN:
                $addPath = FilesHelper::createAdditionalPath(DocumentOutWork::tableName(), FilesHelper::TYPE_SCAN);
                break;
            case FilesHelper::TYPE_DOC:
                $addPath = FilesHelper::createAdditionalPath(DocumentOutWork::tableName(), FilesHelper::TYPE_DOC);
                break;
            case FilesHelper::TYPE_APP:
                $addPath = FilesHelper::createAdditionalPath(DocumentOutWork::tableName(), FilesHelper::TYPE_APP);
                break;
        }

        return FilesHelper::createFileLinks($this, $filetype, $addPath);
    }

    public function getFilesAnswer()
    {
        $repository = Yii::createObject(DocumentOutRepository::class);
        //var_dump($repository->getDocumentInWithoutAnswer());

        return $repository->getDocumentInWithoutAnswer();
    }
    public function getDocumentNumber()
    {
        return $this->document_number;
    }
    public function getDocumentTheme()
    {
        return $this->document_theme;
    }
    public function getCompanyName()
    {
        return $this->companyWork->name;
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
    public function setIsAnswer()
    {
        $this->isAnswer = (Yii::createObject(InOutDocumentsRepository::class))->getByDocumentInId($this->id) ? 1 : 0;
    }
    public function getSendMethodName()
    {
        return Yii::$app->sendMethods->get($this->send_method);
    }

    public function getIsAnswer(int $format = StringFormatter::FORMAT_RAW)
    {
        if($this->is_answer != 0){
            $links = (Yii::createObject(InOutDocumentsRepository::class))->getByDocumentOutId($this->id);

            if($links != null) {
                $str = 'Входящий документ "' . (Yii::createObject(DocumentInRepository::class))->get($links->document_in_id)->document_theme . '"';
                return $format == StringFormatter::FORMAT_LINK ?
                    StringFormatter::stringAsLink($str, Url::to(['document/document-in/view', 'id' => $links->document_in_id])) : $str;
            }
            else {
                return 'Требуется указать ответ';
            }
        }
        return '';
    }
    public function getCompanyWork()
    {
        return $this->hasOne(CompanyWork::class, ['id' => 'company_id']);
    }
    public function generateDocumentNumber(){
        $year = substr(DateFormatter::format($this->document_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash), 0, 4);
        $document_date = DateFormatter::format($this->document_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        $docs = DocumentOutWork::find()->all();
        if($docs == NULL){
            $this->document_number = '1';
            $this->document_postfix = 0;
        }
        else {
            $down = DocumentOutWork::find()
                ->where(['<=', 'document_date', $document_date]) // условие для даты больше заданной
                ->andWhere(['>=', 'document_date', $year."-01-01"]) // начало года
                ->andWhere(['<=', 'document_date', $year."-12-31"]) // конец года
                ->orderBy(['document_date' => SORT_DESC])
                ->all();
            $up = DocumentOutWork::find()
                ->where(['>', 'document_date', $document_date])
                ->andWhere(['>=', 'document_date', $year."-01-01"])
                ->andWhere(['<=', 'document_date', $year."-12-31"])
                ->orderBy(['document_date' => SORT_DESC])
                ->all();
            $down_max = DocumentOutWork::find()
                ->where(['<=', 'document_date', $document_date])
                ->andWhere(['>=', 'document_date', $year."-01-01"])
                ->andWhere(['<=', 'document_date', $year."-12-31"])
                ->max('document_number');
            if($up == null && $down == null) {
                $this->document_number = '1';
                $this->document_postfix = 0;

            }
            if($up == null && $down != null) {

                $this->document_number = $down_max + 1;
                $this->document_postfix = 0;

            }
            if($up != null && $down == null){
                //вопрос???
                $this->document_number = '0';
                $this->document_postfix = '0';

            }
            if($up != null && $down != null){
                $this->document_number = $down_max ;
                $max_postfix  = DocumentOutWork::find()
                    ->where(['<=', 'document_number', $this->document_number])
                    ->andWhere(['>=', 'document_date', $year."-01-01"]) // начало года
                    ->andWhere(['<=', 'document_date', $year."-12-31"]) // конец года
                    ->max('document_postfix');
                $this->document_postfix = $max_postfix + 1;
            }
        }
    }
    /*public function generateDocumentNumber()
    {
        $repository = Yii::createObject(DocumentOutRepository::class);
        $docs = $repository->getAllDocumentsDescDate();
        if (date('Y') !== substr($docs[0]->document_date, 0, 4)) {
            $this->document_number = 1;
        }
        else {
            $docs = $repository->getAllDocumentsInYear();
            if (end($docs)->document_date > $this->document_date && $this->document_theme != 'Резерв') {
                $tempId = 0;
                $tempPre = 0;
                if (count($docs) == 0) {
                    $tempId = 1;
                }
                for ($i = count($docs) - 1; $i >= 0; $i--) {
                    if ($docs[$i]->document_date <= $this->document_date) {
                        $tempId = $docs[$i]->document_number;
                        if ($docs[$i]->document_postfix != null) {
                            $tempPre = $docs[$i]->document_postfix + 1;
                        }
                        else {
                            $tempPre = 1;
                        }
                        break;
                    }
                }

                $this->document_number = $tempId;
                $this->document_postfix = $tempPre;
                Yii::$app->session->addFlash('warning', 'Добавленный документ должен был быть зарегистрирован раньше. Номер документа: '.$this->document_number.'/'.$this->document_postfix);
            }
            else
            {
                if (count($docs) == 0) {
                    $this->document_number = 1;
                }
                else {
                    $this->document_number = end($docs)->document_number + 1;
                }
            }
        }
    }
    */
    public function beforeValidate()
    {
        $this->document_name = 'NAME';
        $this->is_answer = $this->isAnswer;
        $this->creator_id = 1/*Yii::$app->user->identity->getId()*/;
        $this->document_date = DateFormatter::format($this->document_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        $this->sent_date = DateFormatter::format($this->sent_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }
}