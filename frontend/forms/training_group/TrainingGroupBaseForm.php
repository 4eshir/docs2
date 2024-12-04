<?php

namespace frontend\forms\training_group;

use common\events\EventTrait;
use common\helpers\files\FilesHelper;
use yii\base\Model;

class TrainingGroupBaseForm extends Model
{
    use EventTrait;

    public $branch;
    public $budget;
    public $trainingProgramId;
    public $network;
    public $teachers;
    public $endLoadOrders;
    public $startDate;
    public $endDate;
    public $photos;
    public $presentations;
    public $workMaterials;

    public $id;

    public function rules()
    {
        return [
            [['branch', 'budget', 'trainingProgramId', 'network', 'endLoadOrders'], 'integer'],
            [['startDate', 'endDate', 'teachers'], 'safe'],
            [['photos'], 'file',
                'extensions' => 'jpg, jpeg, png, pdf, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true, 'maxSize' => FilesHelper::_MAX_FILE_SIZE, 'maxFiles' => 10],
            [['presentations'], 'file',
                'extensions' => 'jpg, jpeg, png, pdf, ppt, pptx, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true, 'maxSize' => FilesHelper::_MAX_FILE_SIZE, 'maxFiles' => 10],
            [['workMaterials'], 'file',
                'extensions' => 'jpg, jpeg, png, pdf, doc, docx, zip, rar, 7z, tag', 'maxSize' => FilesHelper::_MAX_FILE_SIZE, 'skipOnEmpty' => true, 'maxFiles' => 10],
        ];
    }
}