<?php
namespace app\models\work\order;
use app\models\work\general\OrderPeopleWork;
use app\services\order\OrderMainService;
use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\OrderNumberHelper;
use common\models\scaffold\DocumentOrder;
use common\models\scaffold\OrderMain;
use common\models\scaffold\People;
use common\models\scaffold\PeopleStamp;
use common\repositories\order\OrderMainRepository;
use frontend\models\work\general\PeopleStampWork;
use frontend\models\work\general\PeopleWork;
use InvalidArgumentException;
use Yii;
class OrderMainWork extends DocumentOrderWork
{
    use EventTrait;
    /**
     * Имена файлов для сохранения в БД
     */
    public $responsiblePeople;
    public $names;
    public $orders;
    public $status;
    public $regulations;
    public $archive;
    /**
     * Переменные для input-file в форме
     */
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
            [['docFiles'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10,
                'extensions' => 'xls, xlsx, doc, docx, zip, rar, 7z, tag, txt'],
            [['appFiles'], 'file', 'skipOnEmpty' => true,  'maxFiles' => 10,
                'extensions' => 'ppt, pptx, xls, xlsx, pdf, png, jpg, doc, docx, zip, rar, 7z, tag, txt'],
        ]);
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
    public function beforeValidate()
    {
        $this->order_copy_id = 1;
        $this->type = DocumentOrderWork::ORDER_MAIN;
        $this->order_date = DateFormatter::format($this->order_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        return parent::beforeValidate();
    }
}