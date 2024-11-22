<?php

namespace frontend\models\work\regulation;

use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\StringFormatter;
use common\models\scaffold\Regulation;
use common\repositories\general\FilesRepository;
use frontend\models\work\general\FilesWork;
use InvalidArgumentException;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Url;

class RegulationWork extends Regulation
{
    use EventTrait;
    const STATE_ACTIVE = 1;
    const STATE_EXPIRE = 2;

    public $expires; //документ, отменяющий текущее положение

    /**
     * Переменные для input-file в форме
     */
    public $scanFile;

    public function __construct($config = [])
    {
        parent::__construct($config);
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

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['scanFile'], 'file', 'skipOnEmpty' => true,
                'extensions' => 'png, jpg, pdf, zip, rar, 7z, tag, txt'],
        ]);
    }
    public function getName()
    {
        return $this->name;
    }
    public static function states()
    {
        return [
            self::STATE_ACTIVE => 'Актуально',
            self::STATE_EXPIRE => 'Утратило силу',
        ];
    }

    public function getStates()
    {
        $statuses = self::states();
        if (!array_key_exists($this->state, $statuses)) {
            throw new InvalidArgumentException('Неизвестный статус положения');
        }

        return $statuses[$this->state];
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
            case FilesHelper::TYPE_SCAN:
                $addPath = FilesHelper::createAdditionalPath(RegulationWork::tableName(), FilesHelper::TYPE_SCAN);
                break;
        }

        return FilesHelper::createFileLinks($this, $filetype, $addPath);
    }

    public function beforeSave($insert)
    {
        if ($this->creator_id == null) {
            $this->creator_id = Yii::$app->user->identity->getId();
        }
        $this->last_edit_id = Yii::$app->user->identity->getId();

        return parent::beforeSave($insert); 
    }

    // ТОЛЬКО для предварительной обработки полей. Остальные действия - через Event
    public function beforeValidate()
    {
        $this->creator_id = 1/*Yii::$app->user->identity->getId()*/;
        $this->state = RegulationWork::STATE_ACTIVE;
        $this->date = DateFormatter::format($this->date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        $this->ped_council_date = DateFormatter::format($this->ped_council_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        $this->par_council_date = DateFormatter::format($this->par_council_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        return parent::beforeValidate(); 
    }
}
