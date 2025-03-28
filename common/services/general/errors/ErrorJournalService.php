<?php

namespace common\services\general\errors;

use common\components\dictionaries\base\ErrorDictionary;
use common\helpers\files\FilesHelper;
use common\models\work\ErrorsWork;
use common\repositories\educational\LessonThemeRepository;
use common\repositories\educational\OrderTrainingGroupParticipantRepository;
use common\repositories\educational\TeacherGroupRepository;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\general\ErrorsRepository;
use common\repositories\order\DocumentOrderRepository;
use frontend\models\work\educational\training_group\TeacherGroupWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\event\ForeignEventWork;
use yii\helpers\ArrayHelper;

class ErrorJournalService
{
    private ErrorsRepository $errorsRepository;
    private TrainingGroupRepository $groupRepository;
    private TeacherGroupRepository $teacherGroupRepository;
    private OrderTrainingGroupParticipantRepository $orderParticipantRepository;
    private TrainingGroupLessonRepository $lessonRepository;
    private LessonThemeRepository $themeRepository;

    public function __construct(
        ErrorsRepository $errorsRepository,
        TrainingGroupRepository $groupRepository,
        TeacherGroupRepository $teacherGroupRepository,
        OrderTrainingGroupParticipantRepository $orderParticipantRepository,
        TrainingGroupLessonRepository $lessonRepository,
        LessonThemeRepository $themeRepository
    )
    {
        $this->errorsRepository = $errorsRepository;
        $this->groupRepository = $groupRepository;
        $this->teacherGroupRepository = $teacherGroupRepository;
        $this->orderParticipantRepository = $orderParticipantRepository;
        $this->lessonRepository = $lessonRepository;
        $this->themeRepository = $themeRepository;
    }

    // Проверяет на отсутствие прикрепленного к группе педагога (хотя бы одного)
    public function makeJournal_001($rowId)
    {
        /** @var TeacherGroupWork[] $teachers */
        $teachers = $this->teacherGroupRepository->getAllTeachersFromGroup($rowId);
        if (count($teachers) == 0) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::JOURNAL_001,
                    TrainingGroupWork::tableName(),
                    $rowId
                )
            );
        }
    }

    public function fixJournal_001($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TeacherGroupWork[] $teachers */
        $error = $this->errorsRepository->get($errorId);
        $teachers = $this->teacherGroupRepository->getAllTeachersFromGroup($error->table_row_id);
        if (count($teachers) != 0) {
            $this->errorsRepository->delete($error);
        }
    }

    /*
     * Проверка на отсутствие приказов в группе
     * 1. На момент начала занятий должен быть как минимум 1 приказ о зачислении
     * 2. На момент окончания занятий должно быть как минимум 2 приказа: не менее 1 о зачислении и не менее 1 об отчислении
     */
    public function makeJournal_002($rowId)
    {
        /** @var TrainingGroupWork $group */
        $errFlag = true;
        $group = $this->groupRepository->get($rowId);
        if (date('Y-m-d') >= $group->start_date) {
            $orderEnrollParticipants = $this->orderParticipantRepository->getEnrollByGroupId($rowId);
            if (count($orderEnrollParticipants) >= 1) {
                $errFlag = false;
            }
        }
        if (date('Y-m-d') >= $group->finish_date) {
            $orderExclusionParticipants = $this->orderParticipantRepository->getExlusionByGroupId($rowId);
            if (count($orderExclusionParticipants) >= 1) {
                $errFlag = false;
            }
        }

        if ($errFlag) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::JOURNAL_002,
                    TrainingGroupWork::tableName(),
                    $rowId
                )
            );
        }
    }

    public function fixJournal_002($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $errFlag = false;
        $error = $this->errorsRepository->get($errorId);
        $group = $this->groupRepository->get($error->table_row_id);
        if (!(date('Y-m-d') >= $group->start_date)) {
            $orderEnrollParticipants = $this->orderParticipantRepository->getEnrollByGroupId($error->table_row_id);
            $errFlag = count($orderEnrollParticipants) >= 1;
        }
        if (date('Y-m-d') >= $group->finish_date) {
            $orderExclusionParticipants = $this->orderParticipantRepository->getExlusionByGroupId($error->table_row_id);
            $errFlag = count($orderExclusionParticipants) >= 1;
        }

        if ($errFlag) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие фотоматериалов
    public function makeJournal_003($rowId)
    {
        $daysCount = 4;
        /** @var TrainingGroupWork $group */
        $group = $this->groupRepository->get($rowId);
        if (strtotime($group->finish_date) <= strtotime("+$daysCount days")) {
            if (count($group->getFileLinks(FilesHelper::TYPE_PHOTO)) == 0) {
                $this->errorsRepository->save(
                    ErrorsWork::fill(
                        ErrorDictionary::JOURNAL_003,
                        TrainingGroupWork::tableName(),
                        $rowId
                    )
                );
            }
        }
    }

    public function fixJournal_003($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $error = $this->errorsRepository->get($errorId);
        $group = $this->groupRepository->get($error->table_row_id);
        if (count($group->getFileLinks(FilesHelper::TYPE_PHOTO)) > 0) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие презентационных материалов
    public function makeJournal_004($rowId)
    {
        $daysCount = 4;
        /** @var TrainingGroupWork $group */
        $group = $this->groupRepository->get($rowId);
        if (strtotime($group->finish_date) <= strtotime("+$daysCount days")) {
            if (count($group->getFileLinks(FilesHelper::TYPE_PRESENTATION)) == 0) {
                $this->errorsRepository->save(
                    ErrorsWork::fill(
                        ErrorDictionary::JOURNAL_004,
                        TrainingGroupWork::tableName(),
                        $rowId
                    )
                );
            }
        }
    }

    public function fixJournal_004($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $error = $this->errorsRepository->get($errorId);
        $group = $this->groupRepository->get($error->table_row_id);
        if (count($group->getFileLinks(FilesHelper::TYPE_PRESENTATION)) > 0) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие рабочих материалов
    public function makeJournal_005($rowId)
    {
        $daysCount = 4;
        /** @var TrainingGroupWork $group */
        $group = $this->groupRepository->get($rowId);
        if (strtotime($group->finish_date) <= strtotime("+$daysCount days")) {
            if (count($group->getFileLinks(FilesHelper::TYPE_WORK)) == 0) {
                $this->errorsRepository->save(
                    ErrorsWork::fill(
                        ErrorDictionary::JOURNAL_005,
                        TrainingGroupWork::tableName(),
                        $rowId
                    )
                );
            }
        }
    }

    public function fixJournal_005($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $error = $this->errorsRepository->get($errorId);
        $group = $this->groupRepository->get($error->table_row_id);
        if (count($group->getFileLinks(FilesHelper::TYPE_WORK)) > 0) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на соответствие объема программы и расписания группы
    public function makeJournal_006($rowId)
    {
        /** @var TrainingGroupWork $group */
        $group = $this->groupRepository->get($rowId);
        $lessons = $this->lessonRepository->getLessonsFromGroup($rowId);
        if ($group->trainingProgramWork->capacity != count($lessons)) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::JOURNAL_006,
                    TrainingGroupWork::tableName(),
                    $rowId
                )
            );
        }
    }

    public function fixJournal_006($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $error = $this->errorsRepository->get($errorId);
        $group = $this->groupRepository->get($error->table_row_id);
        $lessons = $this->lessonRepository->getLessonsFromGroup($error->table_row_id);
        if ($group->trainingProgramWork->capacity == count($lessons)) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на заполнение тематического плана группы
    public function makeJournal_007($rowId)
    {
        /** @var TrainingGroupWork $group */
        $lessons = $this->lessonRepository->getLessonsFromGroup($rowId);
        $lessonThemes = $this->themeRepository->getByLessonIds(ArrayHelper::getColumn($lessons, 'id'));
        if (count($lessonThemes) == 0) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::JOURNAL_007,
                    TrainingGroupWork::tableName(),
                    $rowId
                )
            );
        }
    }

    public function fixJournal_007($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var TrainingGroupWork $group */
        $error = $this->errorsRepository->get($errorId);
        $lessons = $this->lessonRepository->getLessonsFromGroup($error->table_row_id);
        $lessonThemes = $this->themeRepository->getByLessonIds(ArrayHelper::getColumn($lessons, 'id'));
        if (count($lessonThemes) != 0) {
            $this->errorsRepository->delete($error);
        }
    }

    public function makeJournal_008($rowId)
    {

    }

    public function fixJournal_008($errorId)
    {

    }

    public function makeJournal_009($rowId)
    {

    }

    public function fixJournal_009($errorId)
    {

    }

    public function makeJournal_010($rowId)
    {

    }

    public function fixJournal_010($errorId)
    {

    }

    public function makeJournal_011($rowId)
    {

    }

    public function fixJournal_011($errorId)
    {

    }

    public function makeJournal_012($rowId)
    {

    }

    public function fixJournal_012($errorId)
    {

    }

    public function makeJournal_013($rowId)
    {

    }

    public function fixJournal_013($errorId)
    {

    }

    public function makeJournal_014($rowId)
    {

    }

    public function fixJournal_014($errorId)
    {

    }

    public function makeJournal_015($rowId)
    {

    }

    public function fixJournal_015($errorId)
    {

    }

    public function makeJournal_016($rowId)
    {

    }

    public function fixJournal_016($errorId)
    {

    }

    public function makeJournal_017($rowId)
    {

    }

    public function fixJournal_017($errorId)
    {

    }

    public function makeJournal_018($rowId)
    {

    }

    public function fixJournal_018($errorId)
    {

    }

    public function makeJournal_019($rowId)
    {

    }

    public function fixJournal_019($errorId)
    {

    }

    public function makeJournal_020($rowId)
    {

    }

    public function fixJournal_020($errorId)
    {

    }

    public function makeJournal_021($rowId)
    {

    }

    public function fixJournal_021($errorId)
    {

    }

    public function makeJournal_022($rowId)
    {

    }

    public function fixJournal_022($errorId)
    {

    }

    public function makeJournal_023($rowId)
    {

    }

    public function fixJournal_023($errorId)
    {

    }

    public function makeJournal_024($rowId)
    {

    }

    public function fixJournal_024($errorId)
    {

    }

    public function makeJournal_025($rowId)
    {

    }

    public function fixJournal_025($errorId)
    {

    }

    public function makeJournal_026($rowId)
    {

    }

    public function fixJournal_026($errorId)
    {

    }

    public function makeJournal_027($rowId)
    {

    }

    public function fixJournal_027($errorId)
    {

    }
}