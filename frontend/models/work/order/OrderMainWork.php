<?php

namespace frontend\models\work\order;

use common\components\dictionaries\base\NomenclatureDictionary;
use common\components\interfaces\FileInterface;
use common\components\traits\ErrorTrait;
use common\helpers\ErrorAssociationHelper;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\helpers\StringFormatter;
use DomainException;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\general\OrderPeopleWork;
use frontend\models\work\regulation\RegulationWork;
use frontend\services\order\OrderMainService;
use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\repositories\order\OrderMainRepository;
use InvalidArgumentException;
use Yii;
use yii\helpers\Url;
use function PHPUnit\Framework\throwException;

/* @property OrderPeopleWork[] $orderPeopleWorks */
class OrderMainWork extends DocumentOrderWork implements FileInterface
{
    use EventTrait, ErrorTrait;

    public $responsible_id;
    public $names;
    public $orders;
    public $status;
    public $regulations;
    public $archive;


    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'fullNumber' => '№ п/п',
            'orderDate' => 'Дата приказа<br>',
            'orderName' => 'Название приказа',
            'bringName' => 'Проект вносит',
            'executorName' => 'Исполнитель',
            'state' => 'Статус',
            'archive' => 'Архивный приказ',
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

    public function getFullName()
    {
        return parent::getFullName(); // TODO: Change the autogenerated stub
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
    public function createReserve()
    {
        $this->order_date = date('Y-m-d');
        $this->order_name = 'Резерв';
        $this->type = DocumentOrderWork::ORDER_MAIN;
        $this->generateOrderNumber();
    }
    public function beforeValidate()
    {
        $post = Yii::$app->request->post();
        $this->order_copy_id = 1;
        $this->type = DocumentOrderWork::ORDER_MAIN;
        if ($post['OrderMainWork']['archive'] == '0') {
            $this->order_number = NomenclatureDictionary::ADMIN_ORDER;
            $this->generateOrderNumber();
        }
        $this->order_date = DateFormatter::format($this->order_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        return parent::beforeValidate();
    }

    /**
     * @return string|void
     */
    public function getDocumentInteractionLink(int $interactionType)
    {
        $expires = $this->expireWorks;
        foreach ($expires as $expire) {
            if ($expire->expire_type == $interactionType) {
                if (!is_null($expire->expire_regulation_id)) {
                    return StringFormatter::stringAsLink(
                        $expire->expireRegulationWork->name,
                        Url::to([Yii::$app->frontUrls::REG_VIEW, 'id' => $expire->expire_regulation_id])
                    );
                }
                if (!is_null($expire->expire_order_id)) {
                    return StringFormatter::stringAsLink(
                        $expire->expireOrderWork->getFullName(),
                        Url::to([Yii::$app->frontUrls::ORDER_MAIN_VIEW, 'id' => $expire->expire_order_id])
                    );
                }
            }
        }
    }

    public function getFilePaths($filetype): array
    {
        return FilesHelper::createFilePaths($this, $filetype, $this->createAddPaths($filetype));
    }

    private function createAddPaths($filetype)
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
        }

        return $addPath;
    }


    public function hasExpire()
    {
        return count($this->expireWorks) > 0;
    }
}