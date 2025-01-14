<?php


namespace frontend\tests\unit\models\training_group;

use common\repositories\educational\GroupProjectThemesRepository;
use common\repositories\educational\TeacherGroupRepository;
use common\repositories\educational\TrainingGroupExpertRepository;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\VisitRepository;
use common\repositories\general\UserRepository;
use common\repositories\providers\group_expert\TrainingGroupExpertMockProvider;
use common\repositories\providers\group_lesson\TrainingGroupLessonMockProvider;
use common\repositories\providers\group_participant\TrainingGroupParticipantMockProvider;
use common\repositories\providers\group_project_themes\GroupProjectThemesMockProvider;
use common\repositories\providers\teacher_group\TeacherGroupMockProvider;
use common\repositories\providers\user\UserMockProvider;
use common\repositories\providers\visit\VisitMockProvider;
use Yii;

class TrainingGroupFullData
{
    protected UserRepository $userRepository;
    protected GroupProjectThemesRepository $groupProjectThemesRepository;
    protected TeacherGroupRepository $teacherGroupRepository;
    protected TrainingGroupExpertRepository $groupExpertRepository;
    protected TrainingGroupLessonRepository $groupLessonRepository;
    protected TrainingGroupParticipantRepository $groupParticipantRepository;
    protected VisitRepository $visitRepository;

    public $group;
    public $teachers;
    public $participants;
    public $lessons;
    public $experts;
    public $themes;

    public function __construct($params = [])
    {
        $this->userRepository = Yii::createObject(
            UserRepository::class,
            ['provider' => Yii::createObject(UserMockProvider::class)]
        );

        $this->groupProjectThemesRepository = Yii::createObject(
            GroupProjectThemesRepository::class,
            ['provider' => Yii::createObject(GroupProjectThemesMockProvider::class)]
        );

        $this->teacherGroupRepository = Yii::createObject(
            TeacherGroupRepository::class,
            ['provider' => Yii::createObject(TeacherGroupMockProvider::class)]
        );

        $this->groupExpertRepository = Yii::createObject(
            TrainingGroupExpertRepository::class,
            ['provider' => Yii::createObject(TrainingGroupExpertMockProvider::class)]
        );

        $this->groupLessonRepository = Yii::createObject(
            TrainingGroupLessonRepository::class,
            ['provider' => Yii::createObject(TrainingGroupLessonMockProvider::class)]
        );

        $this->groupParticipantRepository = Yii::createObject(
            TrainingGroupParticipantRepository::class,
            ['provider' => Yii::createObject(TrainingGroupParticipantMockProvider::class)]
        );

        $this->visitRepository = Yii::createObject(
            VisitRepository::class,
            ['provider' => Yii::createObject(VisitMockProvider::class)]
        );

        $this->fillGroup();
    }

    private function fillGroup()
    {

    }
}