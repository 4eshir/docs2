<?php

namespace app\models\work\order;

use app\services\order\OrderMainService;
use common\components\dictionaries\base\BranchDictionary;
use common\components\dictionaries\base\NomenclatureDictionary;
use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\models\scaffold\OrderMain;
use common\repositories\order\OrderMainRepository;
use frontend\models\work\general\PeopleWork;
use InvalidArgumentException;
use Yii;
/**
 * @property PeopleWork $correspondentWork
 * @property PeopleWork $creatorWork
 * @property PeopleWork $lastUpdateWork
 * @property PeopleWork $executorWork
 * @property PeopleWork $bringWork
 */
class OrderTrainingWork extends OrderMain
{
    use EventTrait;
    public $responsible_id;
    public $branch;
    public $scanFile;
    public $docFiles;
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
    public function getFileLinks($filetype)
    {
        if (!array_key_exists($filetype, FilesHelper::getFileTypes())) {
            throw new InvalidArgumentException('Неизвестный тип файла');
        }
        $addPath = '';
        switch ($filetype) {
            case FilesHelper::TYPE_SCAN:
                $addPath = FilesHelper::createAdditionalPath(OrderTrainingWork::tableName(), FilesHelper::TYPE_SCAN);
                break;
            case FilesHelper::TYPE_DOC:
                $addPath = FilesHelper::createAdditionalPath(OrderTrainingWork::tableName(), FilesHelper::TYPE_DOC);
                break;
            case FilesHelper::TYPE_APP:
                $addPath = FilesHelper::createAdditionalPath(OrderTrainingWork::tableName(), FilesHelper::TYPE_APP);
                break;
        }

        return FilesHelper::createFileLinks($this, $filetype, $addPath);
    }
    public function generateOrderNumber()
    {
        $formNumber = $this->order_number;
        $model_date = DateFormatter::format($this->order_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        $year = substr(DateFormatter::format($model_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash), 0, 4);
        $array_number = [];
        $index = 1;
        $upItem = NULL;
        $equalItem = [];
        $downItem = NULL;
        $isPostfix = NULL;
        $records = Yii::createObject(OrderMainRepository::class)->getEqualPrefix($formNumber);
        $array_number = Yii::createObject(OrderMainService::class)->createArrayNumber($records, $array_number);
        $numberPostfix = Yii::createObject(OrderMainService::class)
            ->createOrderNumber($array_number, $downItem, $equalItem, $upItem, $isPostfix, $index, $formNumber, $model_date);
        $this->order_number = $numberPostfix['number'];
        $this->order_postfix = $numberPostfix['postfix'];
    }
    public function setStatus()
    {
        // зачисление
        if($this->order_number == NomenclatureDictionary::COD_ADD || $this->order_number == NomenclatureDictionary::TECHNOPARK_ADD
         || $this->order_number == NomenclatureDictionary::TECHNOPARK_ADD_BUDGET || $this->order_number == NomenclatureDictionary::QUANTORIUM_ADD
         || $this->order_number == NomenclatureDictionary::CDNTT_ADD || $this->order_number == NomenclatureDictionary::CDNTT_ADD_BUDGET
         || $this->order_number == NomenclatureDictionary::MOB_QUANT_ADD || $this->order_number == NomenclatureDictionary::QUANTORIUM_ADD_BUDGET){
            return 1;
        }
        // отчисление
        if ($this->order_number == NomenclatureDictionary::COD_DEL || $this->order_number == NomenclatureDictionary::TECHNOPARK_DEL
            || $this->order_number == NomenclatureDictionary::TECHNOPARK_DEL_BUDGET || $this->order_number == NomenclatureDictionary::QUANTORIUM_DEL
            || $this->order_number == NomenclatureDictionary::CDNTT_DEL || $this->order_number == NomenclatureDictionary::CDNTT_DEL_BUDGET
            || $this->order_number == NomenclatureDictionary::MOB_QUANT_DEL || $this->order_number == NomenclatureDictionary::QUANTORIUM_DEL_BUDGET) {
            return 2;
        }
        // перевод
        if($this->order_number == NomenclatureDictionary::CDNTT_TRANSFER){
            return 3;
        }


        return 1;
    }
    public function beforeValidate()
    {
        $this->type = self::ORDER_TRAINING;
        $this->order_date = DateFormatter::format($this->order_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }
}