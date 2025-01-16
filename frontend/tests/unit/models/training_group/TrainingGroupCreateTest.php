<?php

namespace frontend\tests\unit\models\training_group;

use common\models\scaffold\People;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\educational\GroupProjectThemesRepository;
use common\repositories\educational\TeacherGroupRepository;
use common\repositories\educational\TrainingGroupExpertRepository;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\educational\VisitRepository;
use common\repositories\providers\group_expert\TrainingGroupExpertMockProvider;
use common\repositories\providers\group_lesson\TrainingGroupLessonMockProvider;
use common\repositories\providers\group_participant\TrainingGroupParticipantMockProvider;
use common\repositories\providers\group_project_themes\GroupProjectThemesMockProvider;
use common\repositories\providers\people\PeopleMockProvider;
use common\repositories\providers\teacher_group\TeacherGroupMockProvider;
use common\repositories\providers\training_group\TrainingGroupMockProvider;
use common\repositories\providers\visit\VisitMockProvider;
use Exception;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\general\PeopleWork;
use Yii;

class TrainingGroupCreateTest extends \Codeception\Test\Unit
{
    protected TrainingGroupRepository $groupRepository;
    protected GroupProjectThemesRepository $groupProjectThemesRepository;
    protected TeacherGroupRepository $teacherGroupRepository;
    protected TrainingGroupExpertRepository $groupExpertRepository;
    protected TrainingGroupLessonRepository $groupLessonRepository;
    protected TrainingGroupParticipantRepository $groupParticipantRepository;
    protected PeopleRepository $peopleRepository;
    protected VisitRepository $visitRepository;

    /**
     * @var \frontend\tests\UnitTester
     */
    protected $tester;

    protected $groupId;
    
    protected function _before()
    {
        $this->groupRepository = Yii::createObject(
            TrainingGroupRepository::class,
            ['groupProvider' => Yii::createObject(TrainingGroupMockProvider::class)]
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

        $this->peopleRepository = Yii::createObject(
            PeopleRepository::class,
            ['provider' => Yii::createObject(PeopleMockProvider::class)]
        );

        $this->visitRepository = Yii::createObject(
            VisitRepository::class,
            ['provider' => Yii::createObject(VisitMockProvider::class)]
        );
    }

    protected function _after()
    {
    }

    // Тестируем создание базовых учебных групп

    /**
     * @dataProvider getCreateGroupData
     */
    public function testCreateGroup(TrainingGroupCreateData $data)
    {
        $groups = $data->groups;

        if (is_array($groups)) {
            foreach ($groups as $item) {
                try {
                    $group = TrainingGroupWork::fill(
                        $item['start_date'],
                        $item['finish_date'],
                        $item['open'],
                        $item['budget'],
                        $item['branch'],
                        $item['order_stop'],
                        $item['archive'],
                        $item['protection_date'],
                        $item['protection_confirm'],
                        $item['is_network'],
                        $item['state'],
                        $item['created_at'],
                        $item['updated_at']
                    );

                    $this->groupId = $this->groupRepository->save($group);
                    $this->assertNotNull($this->groupId, 'Group ID не может быть NULL');
                }
                catch (Exception $exception) {
                    $this->fail('Ошибка сохранения группы: ' . $exception->getMessage());
                }
            }
        }
        else {
            $this->fail('Ошибка провайдера данных');
        }
    }

    public function getCreateGroupData()
    {
        $data = new TrainingGroupCreateData();

        return [
            [
                $data
            ],
        ];
    }

    // Тестируем создание одной учебной группы вместе со всеми связанными данными

    /**
     * @dataProvider getFullGroupData
     */
    public function testFullGroup(TrainingGroupFullData $data)
    {
        foreach ($data->teachers as $people) {
            $this->peopleRepository->save(
                PeopleWork::fill(
                    $people['firstname'],
                    $people['surname'],
                    $people['patronymic']
                )
            );
        }

        var_dump($this->peopleRepository->get(0)->attributes);die;
    }

    public function getFullGroupData()
    {
        $data = new TrainingGroupFullData();

        return [
            [
                $data
            ],
        ];
    }
}