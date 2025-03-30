<?php


namespace common\services\general\errors;

use common\models\work\ErrorsWork;
use common\models\work\UserWork;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\event\EventRepository;
use common\repositories\event\ForeignEventRepository;
use common\repositories\general\ErrorsRepository;
use common\repositories\general\UserRepository;
use common\repositories\rubac\UserPermissionFunctionRepository;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\event\EventWork;
use frontend\models\work\event\ForeignEventWork;
use frontend\models\work\rubac\PermissionFunctionWork;
use frontend\models\work\team\ActParticipantWork;
use Yii;
use yii\helpers\ArrayHelper;

class ErrorService
{
    private ErrorsRepository $errorsRepository;
    private UserRepository $userRepository;
    private EventRepository $eventRepository;
    private ActParticipantRepository $actParticipantRepository;
    private TrainingGroupRepository $groupRepository;

    public function __construct(
        ErrorsRepository $errorsRepository,
        UserRepository $userRepository,
        EventRepository $eventRepository,
        ActParticipantRepository $actParticipantRepository,
        TrainingGroupRepository $groupRepository
    )
    {
        $this->errorsRepository = $errorsRepository;
        $this->userRepository = $userRepository;
        $this->eventRepository = $eventRepository;
        $this->actParticipantRepository = $actParticipantRepository;
        $this->groupRepository = $groupRepository;
    }

    /**
     * Возвращает список ошибок для отправки по e-mail (или как-то еще)
     *
     * @param int $userId
     * @return ErrorsWork[]
     */
    public function getErrorsByUser(int $userId) : array
    {
        /** @var UserWork $user */
        $user = $this->userRepository->get($userId);
        $errorsEvent = [];
        $errorsForeignEvent = [];
        $errorsJournal = [];
        // Поиск ошибок по учету достижения и мероприятиям
        if (Yii::$app->rubac->checkPermission($userId, 'get_achieve_errors')) {
            // Находим ID мероприятий в соответствии с отделом пользователя (или все мероприятия)
            if (Yii::$app->rubac->checkPermission($userId, 'get_all_errors')) {
                // Для того кому надо получать вообще все ошибки
                $eventIds = ArrayHelper::getColumn($this->eventRepository->getAll(), 'id');
                $foreignEventIds = ArrayHelper::getColumn($this->actParticipantRepository->getAll(), 'foreign_event_id');
            }
            else {
                // А это для обычных холопов
                $eventIds = ArrayHelper::getColumn(
                    $this->eventRepository->getEventsByBranches([$user->akaWork->branch]),
                    'id'
                );
                $foreignEventIds = ArrayHelper::getColumn(
                    $this->actParticipantRepository->getActsByBranches([$user->akaWork->branch]),
                    'foreign_event_id'
                );
            }

            // Ищем только те ошибки, которые связаны с найденными мероприятиями
            $errorsEvent = $this->errorsRepository->getErrorsByTableRowsBranch(EventWork::tableName(), $eventIds);
            $errorsForeignEvent = $this->errorsRepository->getErrorsByTableRowsBranch(ForeignEventWork::tableName(), $foreignEventIds);
        }

        // Поиск ошибок по журналу (учебной деятельности)
        if (Yii::$app->rubac->checkPermission($userId, 'get_journal_errors')) {
            if (Yii::$app->rubac->checkPermission($userId, 'get_all_errors')) {
                // Получаем все учебные группы (для суперконтролера)
                $groupIds = ArrayHelper::getColumn($this->groupRepository->getAll(), 'id');
            }
            else if (Yii::$app->rubac->checkPermission($userId, 'get_branch_errors')) {
                // Получаем группы соответствующего отдела (для контролеров в отделе)
                $groupIds = ArrayHelper::getColumn($this->groupRepository->getByBranches([$user->akaWork->branch]), 'id');
            }
            else {
                // Получаем личные группы пользователя (для педагогов)
                $groupIds = ArrayHelper::getColumn($this->groupRepository->getByTeacher($userId), 'id');
            }

            $errorsJournal = $this->errorsRepository->getErrorsByTableRowsBranch(TrainingGroupWork::tableName(), $groupIds);
        }

        // Поиск ошибок по документообороту
        if (Yii::$app->rubac->checkPermission($userId, 'get_document_errors')) {
            // deprecated
        }

        // Поиск ошибок по мат. ценностям
        if (Yii::$app->rubac->checkPermission($userId, 'get_material_errors')) {
            // deprecated
        }

        return array_merge($errorsEvent, $errorsForeignEvent, $errorsJournal);
    }

    public function amnestyErrors(string $tableName, int $rowId)
    {
        /** @var ErrorsWork[] $errors */
        $errors = $this->errorsRepository->getErrorsByTableRow($tableName, $rowId);
        foreach ($errors as $error) {
            $error->setAmnesty();
            $this->errorsRepository->save($error);
            var_dump($error->was_amnesty);
        }
    }

}