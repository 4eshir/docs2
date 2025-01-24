<?php

namespace frontend\forms\event;

use app\models\work\event\ForeignEventWork;
use app\models\work\order\OrderEventWork;
use app\models\work\team\ActParticipantWork;
use app\models\work\team\SquadParticipantWork;
use common\events\EventTrait;
use common\helpers\html\HtmlBuilder;
use common\helpers\StringFormatter;
use common\Model;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\act_participant\SquadParticipantRepository;
use common\repositories\event\ForeignEventRepository;
use common\repositories\event\ParticipantAchievementRepository;
use common\repositories\order\OrderEventRepository;
use frontend\models\work\event\ParticipantAchievementWork;
use frontend\models\work\general\PeopleWork;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class ForeignEventForm extends Model
{
    use EventTrait;

    // Неизменяемые поля
    public $id;
    public $name;
    public $organizer;
    public $startDate;
    public $endDate;
    public $city;
    public $format;
    public $level;
    public $minister;
    public $minAge;
    public $maxAge;
    public OrderEventWork $orderParticipant;

    public string $squadParticipants;

    /** @var SquadParticipantWork[] $squadParticipantsModel */
    public array $squadParticipantsModel;
    public string $oldAchievements;

    // Изменяемые поля
    public $addOrderParticipant;
    public $keyWords;
    public $doc;
    public $escort;
    public $orderBusinessTrip;
    public $isBusinessTrip;

    /** @var ParticipantAchievementWork[] $achievements */
    public array $newAchievements;

    public function __construct($foreignEventId, $config = [])
    {
        parent::__construct($config);
        /** @var OrderEventWork $order */
        /** @var ForeignEventWork $event */
        $event = (Yii::createObject(ForeignEventRepository::class))->get($foreignEventId);
        $order = (Yii::createObject(OrderEventRepository::class))->get($event->order_participant_id);
        $this->id = $foreignEventId;
        $this->name = $order->order_name;
        $this->organizer = $event->organizer_id;
        $this->startDate = $event->begin_date;
        $this->endDate = $event->end_date;
        $this->city = $event->city;
        $this->format = $event->format;
        $this->level = $event->level;
        $this->minister = $event->minister;
        $this->minAge = $event->min_age;
        $this->maxAge = $event->max_age;
        $this->orderParticipant = $order;
        $this->squadParticipantsModel = (Yii::createObject(SquadParticipantRepository::class))->getAllFromEvent($foreignEventId);
        $this->squadParticipants = $this->fillActParticipants($foreignEventId);
        $this->oldAchievements = $this->fillOldAchievements($foreignEventId);

        $this->addOrderParticipant = $event->add_order_participant_id;
        $this->keyWords = $event->key_words;
        $this->escort = $event->escort_id;
        $this->orderBusinessTrip = $event->order_business_trip_id;
    }

    public function fillActParticipants($foreignEventId)
    {
        $actIds = ArrayHelper::getColumn(
            (Yii::createObject(ActParticipantRepository::class))->getByForeignEventId($foreignEventId),
            'id'
        );

        $squads = (Yii::createObject(SquadParticipantRepository::class))->getAllByActIds($actIds);
        return HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Участник'], ArrayHelper::getColumn($squads, 'participantWork.surnameInitials')),
                array_merge(['Отдел(-ы)'], ArrayHelper::getColumn($squads, 'participantWork.surnameInitials')),
                array_merge(['Педагог'], ArrayHelper::getColumn($squads, 'actParticipantWork.teachers')),
                array_merge(['Направленность'], ArrayHelper::getColumn($squads, 'actParticipantWork.focusName')),
                array_merge(['Номинация'], ArrayHelper::getColumn($squads, 'actParticipantWork.nomination')),
                array_merge(['Команда'], ArrayHelper::getColumn($squads, 'participantWork.teamNameWork.name')),
                array_merge(['Форма реализации'], ArrayHelper::getColumn($squads, 'actParticipantWork.formName')),
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Редактировать',
                    Url::to('update-participant'),
                    ['id' => ArrayHelper::getColumn($squads, 'id')])
            ]
        );
    }

    public function fillOldAchievements($foreignEventId)
    {
        $achievements = (Yii::createObject(ParticipantAchievementRepository::class))->getByForeignEvent($foreignEventId);
        $flattenedParticipants = array_map(function ($innerArray) {
            return implode('<br>', array_map(function ($participant) {
                return $participant->participantWork->getFIO(PeopleWork::FIO_SURNAME_INITIALS);
            }, $innerArray));
        }, ArrayHelper::getColumn($achievements, 'actParticipantWork.squadParticipants'));


        return HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Участник'], $flattenedParticipants),
                array_merge(['Статус'], ArrayHelper::getColumn($achievements, 'actParticipantWork.prettyType')),
                array_merge(['Достижение'], ArrayHelper::getColumn($achievements, 'achievement')),
                array_merge(['Акт участия'], ArrayHelper::getColumn($achievements, 'actParticipantWork.string')),
                array_merge(['Номер сертификата'], ArrayHelper::getColumn($achievements, 'cert_number')),
                array_merge(['Дата сертификата'], ArrayHelper::getColumn($achievements, 'date')),
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Редактировать',
                    Url::to('update-achievement'),
                    ['id' => ArrayHelper::getColumn($achievements, 'id')]),
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-achievement'),
                    ['id' => ArrayHelper::getColumn($achievements, 'id')])
            ]
        );
    }
}