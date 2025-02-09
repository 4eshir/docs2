<?php

namespace frontend\models\work\order;

use common\helpers\files\FilesHelper;
use common\models\scaffold\DocumentOrder;
use frontend\models\work\educational\training_group\OrderTrainingGroupParticipantWork;
use frontend\models\work\general\PeopleStampWork;
use frontend\models\work\general\PeopleWork;
use InvalidArgumentException;

class DocumentOrderWork extends DocumentOrder
{
    public const ORDER_MAIN = 1;
    public const ORDER_EVENT = 2;
    public const ORDER_TRAINING = 3;
    /**
     * Переменные для input-file в форме
     */
    public $scanFile;
    public $docFiles;
    public $appFiles;
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['scanFile'], 'file', 'skipOnEmpty' => true,
                'extensions' => 'png, jpg, pdf, zip, rar, 7z, tag, txt'],
            [['docFiles'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10,
                'extensions' => 'xls, xlsx, doc, docx, zip, rar, 7z, tag, txt'],
            [['appFiles'], 'file', 'skipOnEmpty' => true,  'maxFiles' => 10,
                'extensions' => 'ppt, pptx, xls, xlsx, pdf, png, jpg, doc, docx, zip, rar, 7z, tag, txt'],
        ]);
    }
    public $responsiblePeople;
    public function setResponsiblePeople($responsiblePeople)
    {
        $this->responsiblePeople = $responsiblePeople;
    }
    public function getFullOrderName(){
        return $this->order_number . ' ' . $this->order_postfix . ' ' . $this->order_name;
    }
    public function getFullNumber()
    {
        if ($this->order_postfix == null) {
            return $this->order_number;
        }
        else {
            return $this->order_number.'/'.$this->order_postfix;
        }
    }

    public function getFullName()
    {
        $result = $this->getFullNumber();
        return "$result {$this->order_name}";
    }

    public function getOrderDate()
    {
        return $this->order_date;
    }

    public function getNumberPostfix()
    {
        if ($this->order_postfix == null) {
            return $this->order_number;
        }
        else {
            return $this->order_number.'/'.$this->order_postfix;
        }
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
    public function getCreatorWork()
    {
        return PeopleStampWork::findOne($this->creator_id);
    }
    public function getLastUpdateWork()
    {
        return PeopleStampWork::findOne($this->last_edit_id);
    }
    public function getBringWork()
    {
        return PeopleStampWork::findOne($this->bring_id);
    }
    public function getExecutorWork()
    {
        return PeopleStampWork::findOne($this->executor_id);
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
    public function getFileLinks($filetype)
    {
        if (!array_key_exists($filetype, FilesHelper::getFileTypes())) {
            throw new InvalidArgumentException('Неизвестный тип файла');
        }
        $addPath = '';
        switch ($filetype) {
            case FilesHelper::TYPE_SCAN:
                $addPath = FilesHelper::createAdditionalPath(DocumentOrderWork::tableName(), FilesHelper::TYPE_SCAN);
                break;
            case FilesHelper::TYPE_DOC:
                $addPath = FilesHelper::createAdditionalPath(DocumentOrderWork::tableName(), FilesHelper::TYPE_DOC);
                break;
            case FilesHelper::TYPE_APP:
                $addPath = FilesHelper::createAdditionalPath(DocumentOrderWork::tableName(), FilesHelper::TYPE_APP);
                break;
        }
        return FilesHelper::createFileLinks($this, $filetype, $addPath);
    }
}