<?php
namespace app\models\work\order;
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
    public $names;
    public $scanName;
    public $docName;
    public $appName;
    public $archive;
    public $archiveName;
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
            [['scan'], 'file', 'skipOnEmpty' => true,
                'extensions' => 'png, jpg, pdf, zip, rar, 7z, tag, txt'],
            [['doc'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10,
                'extensions' => 'xls, xlsx, doc, docx, zip, rar, 7z, tag, txt']
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
        $model = PeopleWork::findOne($this->bring_id);
        if($model != NULL) {
            return $model->getFullFio();
        }
        else {
           return $this->bring_id;
        }
    }
    public function getResponsiblePeople($post)
    {
        return $post["respPeople"];
    }
    public function getDocumentExpire($post)
    {
        return $post["doc-1"];
    }
    public function getRegulationExpire($post)
    {
        return $post["doc-2"];
    }
    public function getStatusExpire($post)
    {
        return $post["radio"];
    }
    public function getCreatorWork()
    {
        return PeopleWork::findOne($this->creator_id);
    }
    public function getLastUpdateWork()
    {
        return PeopleWork::findOne($this->last_edit_id);
    }
    public function getBringWork()
    {
        return PeopleWork::findOne($this->bring_id);
    }
    public function getExecutorWork()
    {
        return PeopleWork::findOne($this->executor_id);
    }
    public function getExecutorName()
    {
        $model = PeopleWork::findOne($this->executor_id);
        if($model != NULL) {
            return $model->getFullFio();
        }
        else {
            return $this->bring_id;
        }
    }
}