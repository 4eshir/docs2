<?php

namespace backend\tests\unit\report\query;

use common\components\dictionaries\base\BranchDictionary;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\general\UserRepository;
use common\repositories\providers\training_group\TrainingGroupMockProvider;
use common\repositories\providers\user\UserMockProvider;
use Yii;

class ManHoursReportData
{
    protected TrainingGroupRepository $groupRepository;

    public array $groups = [];
    public array $participants = [];
    public array $lessons = [];
    public array $experts = [];
    public array $themes = [];

    public function __construct($params = [])
    {
        $this->fillData();

        $provider = Yii::createObject(TrainingGroupMockProvider::class, [
            'dataStore' => $this->groups,
            'participantsData' => $this->participants,
            'lessonsData' => [],
            'expertsData' => [],
            'themesData' => $this->themes
        ]);

        $this->groupRepository = Yii::createObject(
            TrainingGroupRepository::class, [
                'provider' => $provider
            ]
        );
    }

    private function fillData()
    {
        $this->fillGroups();
        $this->fillParticipants();
        $this->fillThemes();
    }

    /**
     * Создаем mock-модели для как минимум 8 учебных групп
     * Это необходимо для полноценного тестирования отчетных периодов и доп данных
     */
    private function fillGroups()
    {
        $this->groups = [
            [
                'id' => 1,
                'start_date' => '2010-01-01',
                'finish_date' => '2010-06-01',
                'budget' => 1,
                'branch' => BranchDictionary::TECHNOPARK,
            ],
            [
                'id' => 2,
                'start_date' => '2010-01-01',
                'finish_date' => '2010-06-01',
                'budget' => 0,
                'branch' => BranchDictionary::QUANTORIUM,
            ],
            [
                'id' => 3,
                'start_date' => '2010-01-01',
                'finish_date' => '2010-02-01',
                'budget' => 0,
                'branch' => BranchDictionary::TECHNOPARK,
            ],
            [
                'id' => 4,
                'start_date' => '2010-01-01',
                'finish_date' => '2010-02-01',
                'budget' => 1,
                'branch' => BranchDictionary::QUANTORIUM,
            ],
            [
                'id' => 5,
                'start_date' => '2010-04-01',
                'finish_date' => '2010-05-01',
                'budget' => 1,
                'branch' => BranchDictionary::TECHNOPARK,
            ],
            [
                'id' => 6,
                'start_date' => '2010-04-01',
                'finish_date' => '2010-05-01',
                'budget' => 0,
                'branch' => BranchDictionary::QUANTORIUM,
            ],
            [
                'id' => 7,
                'start_date' => '2010-02-15',
                'finish_date' => '2010-04-15',
                'budget' => 0,
                'branch' => BranchDictionary::TECHNOPARK,
            ],
            [
                'id' => 8,
                'start_date' => '2010-02-15',
                'finish_date' => '2010-04-15',
                'budget' => 1,
                'branch' => BranchDictionary::QUANTORIUM,
            ],
        ];
    }

    /**
     * Создаем mock-модели учеников для групп
     */
    private function fillParticipants()
    {
        $this->participants = [
            // Группа 1
            ['id' => 1, 'training_group_id' => 1, 'participant_id' => 1],
            ['id' => 2, 'training_group_id' => 1, 'participant_id' => 2],
            ['id' => 3, 'training_group_id' => 1, 'participant_id' => 3],
            // --------
            // Группа 2
            ['id' => 4, 'training_group_id' => 2, 'participant_id' => 3],
            ['id' => 5, 'training_group_id' => 2, 'participant_id' => 4],
            ['id' => 6, 'training_group_id' => 2, 'participant_id' => 5],
            // --------
            // Группа 3
            ['id' => 7, 'training_group_id' => 3, 'participant_id' => 4],
            ['id' => 8, 'training_group_id' => 3, 'participant_id' => 5],
            ['id' => 9, 'training_group_id' => 3, 'participant_id' => 6],
            // --------
            // Группа 4
            ['id' => 10, 'training_group_id' => 4, 'participant_id' => 7],
            ['id' => 11, 'training_group_id' => 4, 'participant_id' => 8],
            ['id' => 12, 'training_group_id' => 4, 'participant_id' => 9],
            // --------
            // Группа 5
            ['id' => 13, 'training_group_id' => 5, 'participant_id' => 10],
            ['id' => 14, 'training_group_id' => 5, 'participant_id' => 11],
            ['id' => 15, 'training_group_id' => 5, 'participant_id' => 12],
            // --------
            // Группа 6
            ['id' => 16, 'training_group_id' => 6, 'participant_id' => 11],
            ['id' => 17, 'training_group_id' => 6, 'participant_id' => 12],
            ['id' => 18, 'training_group_id' => 6, 'participant_id' => 13],
            // --------
            // Группа 7
            ['id' => 19, 'training_group_id' => 7, 'participant_id' => 13],
            ['id' => 20, 'training_group_id' => 7, 'participant_id' => 14],
            ['id' => 21, 'training_group_id' => 7, 'participant_id' => 15],
            // --------
            // Группа 8
            ['id' => 22, 'training_group_id' => 8, 'participant_id' => 14],
            ['id' => 23, 'training_group_id' => 8, 'participant_id' => 15],
            ['id' => 24, 'training_group_id' => 8, 'participant_id' => 16],
            // --------
        ];
    }

    /**
     * Создаем mock-модели тематического плана
     */
    private function fillThemes()
    {
        $this->themes = [
            // Группа 1
            ['id' => 1, 'training_group_id' => 1, 'teacher_id' => 1],
            ['id' => 2, 'training_group_id' => 1, 'teacher_id' => 1],
            // --------
            // Группа 2
            ['id' => 3, 'training_group_id' => 2, 'teacher_id' => 2],
            ['id' => 4, 'training_group_id' => 2, 'teacher_id' => 2],
            // --------
            // Группа 3
            ['id' => 5, 'training_group_id' => 3, 'teacher_id' => 3],
            ['id' => 6, 'training_group_id' => 3, 'teacher_id' => 3],
            // --------
            // Группа 4
            ['id' => 7, 'training_group_id' => 4, 'teacher_id' => 1],
            ['id' => 8, 'training_group_id' => 4, 'teacher_id' => 2],
            // --------
            // Группа 5
            ['id' => 9, 'training_group_id' => 5, 'teacher_id' => 2],
            ['id' => 10, 'training_group_id' => 5, 'teacher_id' => 3],
            // --------
            // Группа 6
            ['id' => 11, 'training_group_id' => 6, 'teacher_id' => 3],
            ['id' => 12, 'training_group_id' => 6, 'teacher_id' => 1],
            // --------
            // Группа 7
            ['id' => 13, 'training_group_id' => 7, 'teacher_id' => 3],
            ['id' => 14, 'training_group_id' => 7, 'teacher_id' => 2],
            // --------
            // Группа 8
            ['id' => 15, 'training_group_id' => 8, 'teacher_id' => 2],
            ['id' => 16, 'training_group_id' => 8, 'teacher_id' => 1],
            // --------
        ];
    }
}