<?php
namespace frontend\models\work\order;
use common\events\EventTrait;
use common\models\scaffold\OrderMain;
use frontend\models\work\general\PeopleWork;

/**
 * @property PeopleWork $correspondentWork
 * @property PeopleWork $creatorWork
 * @property PeopleWork $lastUpdateWork
 * @property PeopleWork $executorWork
 * @property PeopleWork $bringWork
 *
 *
 */
class OrderMainWork extends OrderMain
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
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'fullNumber' => '№ п/п',
            'orderDate' => 'Дата приказа<br>',
            'orderName' => 'Название приказа',
            'bringName' => 'Проект вносит',
            'executorName' => 'Исполнитель',
            'state' => 'Статус'
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
    public function getFullNumber()
    {
        if ($this->order_postfix == null)
            return $this->order_number;
        else
            return $this->order_number.'/'.$this->order_postfix;
    }
    public function getOrderDate()
    {
        return $this->order_date;
    }

    public function getOrderName()
    {
        return $this->order_name;
    }
    public function getBringName()
    {
        return $this->bring_id;
    }
    public function getExecutorName()
    {
        return $this->executor_id;
    }
}