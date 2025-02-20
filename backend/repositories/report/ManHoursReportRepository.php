<?php

namespace backend\repositories\report;

use common\repositories\educational\LessonThemeRepository;
use common\repositories\educational\TeacherGroupRepository;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingProgramRepository;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

class ManHoursReportRepository
{
    private TrainingGroupLessonRepository $lessonRepository;
    private LessonThemeRepository $lessonThemeRepository;
    private TrainingProgramRepository $programRepository;

    public function __construct(
        TrainingGroupLessonRepository $lessonRepository,
        LessonThemeRepository $lessonThemeRepository,
        TrainingProgramRepository $programRepository
    )
    {
        $this->lessonRepository = $lessonRepository;
        $this->lessonThemeRepository = $lessonThemeRepository;
        $this->programRepository = $programRepository;
    }

    /**
     * @param ActiveQuery $query
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findAll(ActiveQuery $query) : array
    {
        return $query->all();
    }

    /**
     * @param ActiveQuery $query
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findOne(ActiveQuery $query) : array
    {
        return $query->one();
    }

    /**
     * @return ActiveQuery
     */
    public function searchTrainingGroups()
    {
        return TrainingGroupWork::find();
    }

    /**
     * Фильтр учебных групп по отделам
     *
     * @param ActiveQuery $query
     * @param int[] $branches
     * @return ActiveQuery
     */
    public function filterGroupsByBranches(ActiveQuery $query, array $branches)
    {
        return $query->andWhere(['IN', 'branch', $branches]);
    }

    /**
     * Фильтр учебных групп по основе (бюджет/внебюджет)
     *
     * @param ActiveQuery $query
     * @param int[] $budget
     * @return ActiveQuery
     */
    public function filterGroupsByBudget(ActiveQuery $query, array $budget)
    {
        return $query->andWhere(['IN', 'budget', $budget]);
    }

    /**
     * Фильтр учебных групп по направленности (проверяется образовательная программа)
     *
     * @param ActiveQuery $query
     * @param int[] $focuses
     * @return ActiveQuery
     */
    public function filterGroupsByFocuses(ActiveQuery $query, array $focuses)
    {
        $programIds = ArrayHelper::getColumn(
            $this->programRepository->getByFocuses($focuses),
            'id'
        );

        return $query->andWhere(['IN', 'training_program_id', $programIds]);
    }

    /**
     * Фильтр учебных групп по форме реализации (проверяется образовательная программа)
     *
     * @param ActiveQuery $query
     * @param int[] $allowRemotes
     * @return ActiveQuery
     */
    public function filterGroupsByAllowRemote(ActiveQuery $query, array $allowRemotes)
    {
        $programIds = ArrayHelper::getColumn(
            $this->programRepository->getByAllowRemotes($allowRemotes),
            'id'
        );

        return $query->andWhere(['IN', 'training_program_id', $programIds]);
    }

    /**
     * Фильтр учебных групп по датам
     * Если группа любой частью срока обучения попадает в данный промежуток - она будет учтена
     *
     * @param ActiveQuery $query
     * @param string $date1
     * @param string $date2
     * @return ActiveQuery
     */
    public function filterGroupsBetweenDates(ActiveQuery $query, string $date1, string $date2)
    {
        return $query->andWhere(['BETWEEN', 'start_date', $date1, $date2])
            ->orWhere(['BETWEEN', 'finish_date', $date1, $date2])
            ->orWhere(['and',
                ['<', 'start_date', $date1],
                ['>', 'finish_date', $date2]
            ]);
    }
}