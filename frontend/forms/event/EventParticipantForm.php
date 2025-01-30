<?php

namespace frontend\forms\event;

use app\models\work\team\ActParticipantWork;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\act_participant\SquadParticipantRepository;
use Yii;
use yii\base\Model;

class EventParticipantForm extends Model
{
    private ActParticipantRepository $actParticipantRepository;
    private SquadParticipantRepository $squadParticipantRepository;

    private ActParticipantWork $actParticipants;

    public function __construct(
        $actParticipantId,
        ActParticipantRepository $actParticipantRepository = null,
        SquadParticipantRepository $squadParticipantRepository = null,
        $config = [])
    {
        parent::__construct($config);
        if (!$actParticipantRepository) {
            $actParticipantRepository = Yii::createObject(ActParticipantRepository::class, ['provider' => Yii::createObject(ActPa)])
        }
    }
}