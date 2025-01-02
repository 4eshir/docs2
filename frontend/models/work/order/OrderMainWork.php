<?php
namespace app\models\work\order;
use app\models\work\general\OrderPeopleWork;
use app\services\order\OrderMainService;
use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\OrderNumberHelper;
use common\models\scaffold\OrderMain;
use common\models\scaffold\People;
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
    public $orders;
    public $status;
    public $regulations;
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
            [['scanFile'], 'file', 'skipOnEmpty' => true,
                'extensions' => 'png, jpg, pdf, zip, rar, 7z, tag, txt'],
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
    public function getResponsiblePeople($post)
    {
        return $post["OrderMainWork"]["names"];
    }
    public function getDocumentExpire($post)
    {

        return $post["OrderMainWork"]["orders"];
    }
    public function getRegulationExpire($post)
    {

        return $post["OrderMainWork"]["regulations"];
    }
    public function getStatusExpire($post)
    {
        return $post["OrderMainWork"]["radio"];
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
                $addPath = FilesHelper::createAdditionalPath(OrderMainWork::tableName(), FilesHelper::TYPE_SCAN);
                break;
            case FilesHelper::TYPE_DOC:
                $addPath = FilesHelper::createAdditionalPath(OrderMainWork::tableName(), FilesHelper::TYPE_DOC);
                break;
            case FilesHelper::TYPE_APP:
                $addPath = FilesHelper::createAdditionalPath(OrderMainWork::tableName(), FilesHelper::TYPE_APP);
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

    public function getNameWithNumber()
    {
        if ($this->order_postfix != null){
            $result = $this->order_number . '/' . $this->order_postfix.' '.$this->order_name;
        }
        else {
            $result = $this->order_number.' '.$this->order_name;
        }
        return $result;
    }

    public function beforeValidate()
    {
        $this->order_copy_id = 1;
        $this->order_date = DateFormatter::format($this->order_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        return parent::beforeValidate(); 
    }
}