<?php

namespace frontend\models\work\event;

use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\StringFormatter;
use common\models\scaffold\Event;
use common\models\scaffold\User;
use common\repositories\event\EventRepository;
use common\repositories\general\FilesRepository;
use common\repositories\regulation\RegulationRepository;
use frontend\models\work\general\FilesWork;
use frontend\models\work\general\PeopleStampWork;
use frontend\models\work\general\PeopleWork;
use frontend\models\work\general\UserWork;
use frontend\models\work\order\OrderMainWork;
use InvalidArgumentException;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/** @property UserWork $creatorWork */
/** @property PeopleStampWork $responsible1Work */
/** @property PeopleStampWork $responsible2Work */
/** @property OrderMainWork $orderWork */

class EventWork extends Event
{
    use EventTrait;

    public $protocolFiles;
    public $reportingFiles;
    public $photoFiles;
    public $otherFiles;

    public $scopes;
    public $branches;

    public $isTechnopark;
    public $isQuantorium;
    public $isCDNTT;
    public $isMobQuant;
    public $isCod;

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
                [['scopes', 'branches'], 'safe'],
                ['child_rst_participants_count', 'compare', 'compareAttribute' => 'child_participants_count', 'operator' => '<=', 'message' => 'Количество детей от РШТ не должно превышать общего количества детей'],
            ]
        );
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name' => 'Название<br>мероприятия',
            'datePeriod' => 'Период<br>проведения',
            'eventType' => 'Тип<br>мероприятия',
            'scopesSplitter' => 'Тематическая<br>направленность',
            'responsibleString' => 'Ответственный(-ые)<br>работник(-и)',
            'eventBranches' => 'Мероприятие проводит',
            'regulationRaw' => 'Положение',
            'address' => 'Адрес<br>проведения',
            'eventLevel' => 'Уровень<br>мероприятия',
            'participantCount' => 'Кол-во<br>участников',
            'isFederal' => 'Входит<br>в ФП',
            'orderName' => 'Приказ',
            'eventWay' => 'Формат<br>проведения',
            'eventLevelAndType' => 'Уровень и Тип<br>мероприятия',
        ]);
    }

    public function getDatePeriod() {
        return DateFormatter::format($this->start_date, DateFormatter::Ymd_dash, DateFormatter::dmy_dot)
            . ' - ' . DateFormatter::format($this->finish_date, DateFormatter::Ymd_dash, DateFormatter::dmy_dot);
    }

    public function getEventType()
    {
        return Yii::$app->eventType->get($this->event_type);
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getEventLevel()
    {
        return Yii::$app->eventLevel->get($this->event_level);
    }

    public function getParticipantCount()
    {
        return $this->participant_count;
    }

    public function getOrderName()
    {
        $order = $this->orderWork;
        return $order ? $order->getFullName() : '---';
    }

    public function getEventWay()
    {
        return Yii::$app->eventWay->get($this->event_way);
    }

    public function getEventLevelAndType()
    {
        return $this->getEventLevel() . '<br>' . $this->getEventType();
    }

    public function getIsFederal()
    {
        return $this->is_federal == 1 ? 'Да' : 'Нет';
    }

    public function getResponsible1Work()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'responsible1_id']);
    }

    public function getResponsible2Work()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'responsible2_id']);
    }

    public function getEventBranches()
    {
        $eventBranches = (Yii::createObject(EventRepository::class))->getBranches($this->id);

        $result = '';
        $branches = ArrayHelper::getColumn($eventBranches, 'branch');
        foreach ($branches as $branch) {
            $result .= Yii::$app->branches->get($branch) . ' ';
        }

        return $result;
    }

    public function getRegulationRaw()
    {
        $regulation = (Yii::createObject(RegulationRepository::class))->get($this->regulation_id);

        return $regulation ?
            StringFormatter::stringAsLink("Положение '$regulation->name'", Url::to(['regulation/regulation/view', 'id' => $regulation->id])) :
            'Нет';
    }

    public function getResponsibles()
    {
        $resbonsibles = [];
        if ($this->responsible1_id) {
            $resbonsibles[] = StringFormatter::stringAsLink($this->responsible1Work->peopleWork->getFio(PeopleWork::FIO_SURNAME_INITIALS), Url::to(['dictionaries/people/view', 'id' => $this->responsible1Work->people_id]));
        }
        if ($this->responsible2_id) {
            $resbonsibles[] = StringFormatter::stringAsLink($this->responsible2Work->peopleWork->getFio(PeopleWork::FIO_SURNAME_INITIALS), Url::to(['dictionaries/people/view', 'id' => $this->responsible2Work->people_id]));
        }

        return implode('<br>', $resbonsibles);
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
            case FilesHelper::TYPE_PROTOCOL:
                $addPath = FilesHelper::createAdditionalPath(EventWork::tableName(), FilesHelper::TYPE_PROTOCOL);
                break;
            case FilesHelper::TYPE_PHOTO:
                $addPath = FilesHelper::createAdditionalPath(EventWork::tableName(), FilesHelper::TYPE_PHOTO);
                break;
            case FilesHelper::TYPE_REPORT:
                $addPath = FilesHelper::createAdditionalPath(EventWork::tableName(), FilesHelper::TYPE_REPORT);
                break;
            case FilesHelper::TYPE_OTHER:
                $addPath = FilesHelper::createAdditionalPath(EventWork::tableName(), FilesHelper::TYPE_OTHER);
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

    public function beforeValidate()
    {
        if ($this->order_id == '') $this->order_id = null;
        if ($this->regulation_id == '') $this->regulation_id = null;
        $this->start_date = DateFormatter::format($this->start_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        $this->finish_date = DateFormatter::format($this->finish_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        return parent::beforeValidate(); 
    }

    public function fillSecondaryFields()
    {
        $branches = ArrayHelper::getColumn((Yii::createObject(EventRepository::class))->getBranches($this->id), 'branch');
        $scopes = ArrayHelper::getColumn((Yii::createObject(EventRepository::class))->getScopes($this->id), 'participation_scope');

        $branchArray = [];
        foreach (array_keys(Yii::$app->branches->getList()) as $branch) {
            if (in_array($branch, $branches)) {
                $branchArray[] = $branch;
            }
        }

        $scopesArray = [];
        foreach (array_keys(Yii::$app->participationScope->getList()) as $scope) {
            if (in_array($scope, $scopes)) {
                $scopesArray[] = $scope;
            }
        }

        $this->branches = $branchArray;
        $this->scopes = $scopesArray;
    }

    public function getCreatorWork()
    {
        return $this->hasOne(UserWork::class, ['id' => 'creator_id']);
    }

    public function setValuesForUpdate()
    {
        $this->responsible1_id = $this->responsible1Work->people_id;
        $this->responsible2_id = $this->responsible2Work->people_id;
    }

    public function getOrderWork()
    {
        return $this->hasOne(OrderMainWork::class, ['id' => 'order_id']);
    }
}