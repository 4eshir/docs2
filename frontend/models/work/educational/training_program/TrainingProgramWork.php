<?php

namespace frontend\models\work\educational\training_program;

use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\models\scaffold\TrainingProgram;
use common\repositories\educational\TrainingProgramRepository;
use common\services\general\files\FileService;
use frontend\models\work\general\PeopleStampWork;
use InvalidArgumentException;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/** @property PeopleStampWork $authorWork */

class TrainingProgramWork extends TrainingProgram
{
    use EventTrait;

    public $mainFile;
    public $docFiles;
    public $contractFile;
    public $utpFile;

    public $branches;

    public $themes;
    public $controls;
    public $authors;

    private FileService $fileService;
    private TrainingProgramRepository $repository;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->fileService = Yii::createObject(FileService::class);
        $this->repository = Yii::createObject(TrainingProgramRepository::class);
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
                [['branches'], 'safe'],
                [['mainFile'], 'file', 'skipOnEmpty' => true,
                    'extensions' => 'jpg, png, pdf, doc, docx, zip, rar, 7z, tag'],
                [['docFiles'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10,
                    'extensions' => 'jpg, png, pdf, doc, docx, zip, rar, 7z, tag'],
                [['contractFile'], 'file', 'skipOnEmpty' => true,
                    'extensions' => 'ppt, pptx, xls, xlsx, pdf, png, jpg, doc, docx, zip, rar, 7z, tag, txt'],
                [['utpFile'], 'file', 'extensions' => 'xls, xlsx', 'skipOnEmpty' => true],
            ]
        );
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => function() {
                    return date('Y-m-d H:i:s');
                },
            ],
        ];
    }
    public function getAuthorWork()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'author_id']);
    }

    public function beforeSave($insert)
    {
        if ($this->creator_id == null) {
            $this->creator_id = Yii::$app->user->identity->getId();
        }
        $this->last_edit_id = Yii::$app->user->identity->getId();

        return parent::beforeSave($insert);
    }

    public function beforeValidate()
    {
        $this->ped_council_date = DateFormatter::format($this->ped_council_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        return parent::beforeValidate();
    }

    public function getFullDirectionName()
    {
        return Yii::$app->thematicDirection->getFullnameList()[$this->thematic_direction];
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
            case FilesHelper::TYPE_MAIN:
                $addPath = FilesHelper::createAdditionalPath(TrainingProgramWork::tableName(), FilesHelper::TYPE_MAIN);
                break;
            case FilesHelper::TYPE_DOC:
                $addPath = FilesHelper::createAdditionalPath(TrainingProgramWork::tableName(), FilesHelper::TYPE_DOC);
                break;
            case FilesHelper::TYPE_CONTRACT:
                $addPath = FilesHelper::createAdditionalPath(TrainingProgramWork::tableName(), FilesHelper::TYPE_CONTRACT);
                break;
        }

        return FilesHelper::createFileLinks($this, $filetype, $addPath);
    }
}