<?php

namespace frontend\forms\journal;

use common\Model;
use common\repositories\educational\VisitRepository;
use frontend\models\work\educational\journal\ParticipantLessons;
use frontend\models\work\educational\journal\VisitLesson;
use frontend\models\work\educational\journal\VisitWork;
use Yii;

class JournalForm extends Model
{
    /** @var VisitWork[] $visits */
    public array $visits;
    public $groupId;

    /** @var ParticipantLessons[] $participantLessons */
    public array $participantLessons;

    public function __construct($groupId = null, $config = [])
    {
        parent::__construct($config);
        if ($groupId !== null) {
            $this->groupId = $groupId;
            $this->visits = (Yii::createObject(VisitRepository::class))->getByTrainingGroup($this->groupId);

            foreach ($this->visits as $visit) {
                $lessons = VisitLesson::fromString($visit->lessons);
                $this->participantLessons[] = new ParticipantLessons(
                    $visit->participant_id,
                    $lessons
                );
            }
        }
    }
}