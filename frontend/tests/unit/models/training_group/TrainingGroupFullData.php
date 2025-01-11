<?php


namespace frontend\tests\unit\models\training_group;

use common\repositories\educational\GroupProjectThemesRepository;
use common\repositories\educational\TeacherGroupRepository;
use common\repositories\educational\TrainingGroupExpertRepository;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\VisitRepository;
use common\repositories\general\UserRepository;
use common\repositories\providers\user\UserMockProvider;
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
            ['userProvider' => Yii::createObject(UserMockProvider::class)]
        );

        $this->fillGroup();
    }

    private function fillGroup()
    {

    }
}