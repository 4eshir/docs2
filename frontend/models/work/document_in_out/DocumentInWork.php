<?php

namespace frontend\models\work\document_in_out;

use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\helpers\StringFormatter;
use common\models\scaffold\DocumentIn;
use common\repositories\document_in_out\DocumentOutRepository;
use common\repositories\document_in_out\InOutDocumentsRepository;
use common\repositories\general\FilesRepository;
use frontend\models\work\dictionaries\CompanyWork;
use frontend\models\work\dictionaries\PositionWork;
use frontend\models\work\general\FilesWork;
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

    /**
     * Возвращает массив
     * link => форматированная ссылка на документ
     * id => ID записи в таблице files
     * @param $filetype
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getFileLinks($filetype)
    {
        if (!array_key_exists($filetype, FilesHelper::getFileTypes())) {
            throw new InvalidArgumentException('Неизвестный тип файла');
        }

        $addPath = '';
        switch ($filetype) {
            case FilesHelper::TYPE_SCAN:
                $addPath = FilesHelper::createAdditionalPath(DocumentInWork::tableName(), FilesHelper::TYPE_SCAN);
                break;
            case FilesHelper::TYPE_DOC:
                $addPath = FilesHelper::createAdditionalPath(DocumentInWork::tableName(), FilesHelper::TYPE_DOC);
                break;
            case FilesHelper::TYPE_APP:
                $addPath = FilesHelper::createAdditionalPath(DocumentInWork::tableName(), FilesHelper::TYPE_APP);
                break;
        }

        return FilesHelper::createFileLinks($this, $filetype, $addPath);
    }

    /**
     * Возвращает строку с отображением необходимости ответа на входящий документ
     * @param int $format формат возвращаемого значения (при наличии такой опции) @see StringFormatter
     * @return string
     */
    public function getNeedAnswer(int $format = StringFormatter::FORMAT_RAW)
    {
        if($this->need_answer != 0){
            $links = (Yii::createObject(InOutDocumentsRepository::class))->getByDocumentInId($this->id);

            if($links->document_out_id != null) {
                $str = 'Исходящий документ "' . (Yii::createObject(DocumentOutRepository::class))->get($links->document_out_id)->document_theme . '"';
                return $format == StringFormatter::FORMAT_LINK ?
                    StringFormatter::stringAsLink($str, Url::to(['document/document-out/view', 'id' => $links->document_out_id])) : $str;
            }
            else {
                return 'Требуется указать ответ до '. DateFormatter::format($links->date, DateFormatter::Ymd_dash, DateFormatter::dmY_dot);
            }
        }
        return '';
    }
    public function TestIn(){
        $year = substr(DateFormatter::format($this->local_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash), 0, 4);
        $local_date = DateFormatter::format($this->local_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        $docs = DocumentInWork::find()->all();
        if($docs == NULL){
            $this->local_number = '1';
            $this->local_postfix = 0;
        }
        else {
            $down = DocumentInWork::find()
                ->where(['<', 'local_date', $local_date]) // условие для даты больше заданной
                ->andWhere(['>=', 'local_date', $year."-01-01"]) // начало года
                ->andWhere(['<=', 'local_date', $year."-12-31"]) // конец года
                ->orderBy(['local_date' => SORT_DESC])
                ->all();
            $up = DocumentInWork::find()
                ->where(['>', 'local_date', $local_date])
                ->andWhere(['>=', 'local_date', $year."-01-01"])
                ->andWhere(['<=', 'local_date', $year."-12-31"])
                ->orderBy(['local_date' => SORT_DESC])
                ->all();
            $down_max = DocumentInWork::find()
                ->where(['<=', 'local_date', $local_date])
                ->andWhere(['>=', 'local_date', $year."-01-01"])
                ->andWhere(['<=', 'local_date', $year."-12-31"])
                ->max('local_number');
            if($up == null && $down == null) {
                $this->local_number = '0';
                $this->local_postfix = 0;
            }
            if($up == null && $down != null) {

                $this->local_number = $down_max + 1;
                $this->local_postfix = 0;
            }
            if($up != null && $down == null){
                $this->local_number = '0';
                $this->local_postfix = '0';
            }
            if($up != null && $down != null){
                $this->local_number = $down_max ;
                $max_postfix  = DocumentInWork::find()
                    ->where(['<=', 'local_number', $this->local_number])
                    ->andWhere(['>=', 'local_date', $year."-01-01"]) // начало года
                    ->andWhere(['<=', 'local_date', $year."-12-31"]) // конец года
                    ->max('local_postfix');
                $this->local_postfix = $max_postfix + 1;
            }
        }
    }

    public function createGroupButton()
    {
        $links = [
            'Добавить документ' => Url::to(['document/document-in/create']),
            'Добавить резерв' => Url::to(['document/document-in/reserve']),
        ];
        return HtmlBuilder::createGroupButton($links);
    }

    /*public function getLastNumber($inputString) {
        $parts = explode('/', $inputString);
        return $parts;
    }
    public function maxPostfix($model) {
        $max = '0';
        foreach ($model as $doc){
            $parts = $this->getLastNumber($doc->local_postfix);
            $parts_max = $this->getLastNumber($max);
            $length = count($parts);
            $max_length = count($parts_max);
            if($length > $max_length){
                $max = $doc->local_postfix;
            }
            else if($length = $max_length){
                $parts_max = $this->getLastNumber($max);
                for($i = 0; $i < $length; $i++){
                    if((int)$parts[$i] > (int)$parts_max[$i]){
                        $max = $doc->local_postfix;
                        break;
                    }
                }
            }
        }
        return $max;
    }
    public function getIncrementedLastNumberString($inputString) {
        $parts = explode('/', $inputString);
        $lastNumber = end($parts) + 1;
        $parts[count($parts) - 1] = $lastNumber;
        return implode('/', $parts);
    }*/
    public function beforeValidate()
    {
        $this->creator_id = 1/*Yii::$app->user->identity->getId()*/;
        $this->local_date = DateFormatter::format($this->local_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        $this->real_date = DateFormatter::format($this->real_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        $this->dateAnswer = DateFormatter::format($this->dateAnswer, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
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