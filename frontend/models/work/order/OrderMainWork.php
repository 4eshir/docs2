<?php
namespace app\models\work\order;
use app\models\work\general\OrderPeopleWork;
use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\models\scaffold\OrderMain;
use common\models\scaffold\People;
use frontend\models\work\general\PeopleWork;
use InvalidArgumentException;
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
            [['docFiles'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10,
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
        $model_date = DateFormatter::format($this->order_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        $year = substr(DateFormatter::format($model_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash), 0, 4);
        $array_number = [];
        $upItem = NULL;
        $equalItem = [];
        $downItem = NULL;
        $number = NULL;
        $postfix = NULL;
        $records = OrderMainWork::find()
            ->where(['like', 'order_number', '02-02%', false])
            ->orderBy(['order_date' => SORT_ASC])
            ->all();
        foreach ($records as $record) {
            /* @var \app\models\work\order\OrderMainWork $record */
            if($record->order_postfix == NULL) {
                array_push($array_number, [
                    $record->order_date,
                    $record->order_number,
                    $record->order_number
                ]);
            }
            else {
                array_push($array_number, [
                    $record->order_date,
                    $record->order_number,
                    $record->order_number.'/'.$record->order_postfix
                ]);
            }
        }
        for ($i = 0; $i < count($array_number); $i++) {
            $item = $array_number[$i];
            if ($item[0] < $model_date) {
                $downItem = $item;
            }
            if ($item[0] == $model_date) {
                array_push($equalItem, $item);
            }
            if ($item[0] > $model_date) {
                $upItem = $item;
                break;
            }
        }
        $this->sortArrayByOrderNumber($equalItem);
        if($equalItem != NULL) {
            $downItem = $equalItem[count($equalItem) - 1];
        }
        $newNumber = $downItem[2];
        $index = 1;
        while ($this->findByNumberPostfix($array_number, $newNumber)) {
            $parts = $this->splitString($newNumber);
            $number = $parts[0];
            for ($i = 1; $i < count($parts) - 1; $i++) {
                $number = $number . '/' . (string)$parts[$i];
            }
            $number = $number.'/'.(string)$index;
            if($upItem[2] < $number.'/'.(string)$index) {
                $number = $number.'/'.(string)$index;
            }
            else {
                $number = $newNumber.'/'.'1';
            }
            $newNumber = $number;
            $index++;
        }
    }
    function splitString($input) {
        // Используем функцию explode для разделения строки по символу '/'
        $words = explode('/', $input);
        return $words;
    }
    function sortArrayByOrderNumber(&$array) {
        if($array != NULL) {
            usort($array, function ($a, $b) {
                return strcmp($a[1], $b[1]); // Сравниваем элементы с индексом 1, которые соответствуют order_number
            });
        }
    }
    public function findByNumberPostfix($array, $numberPostfix)
    {
        if($array != NULL) {
            foreach ($array as $item) {
                if($item[2] == $numberPostfix) {
                    return true;
                }
            }
        }
        else {
            return false;
        }
    }
    public function beforeValidate()
    {
        $this->order_copy_id = 1;
        $this->order_date = DateFormatter::format($this->order_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }
}