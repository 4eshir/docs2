<?php


namespace common\services\general\errors;

use common\models\work\UserWork;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\event\EventRepository;
use common\repositories\event\ForeignEventRepository;
use common\repositories\general\ErrorsRepository;
use common\repositories\general\UserRepository;
use common\repositories\rubac\UserPermissionFunctionRepository;
use frontend\models\work\event\EventWork;
use frontend\models\work\event\ForeignEventWork;
use frontend\models\work\rubac\PermissionFunctionWork;
use frontend\models\work\team\ActParticipantWork;
use Yii;
use yii\helpers\ArrayHelper;

class ErrorSendService
{
    private ErrorsRepository $errorsRepository;
    private UserRepository $userRepository;
    private EventRepository $eventRepository;
    private ActParticipantRepository $actParticipantRepository;

    public function __construct(
        ErrorsRepository $errorsRepository,
        UserRepository $userRepository,
        EventRepository $eventRepository,
        ActParticipantRepository $actParticipantRepository
    )
    {
        $this->errorsRepository = $errorsRepository;
        $this->userRepository = $userRepository;
        $this->eventRepository = $eventRepository;
        $this->actParticipantRepository = $actParticipantRepository;
    }

    public function getErrorsByUser(int $userId)
    {
        /** @var UserWork $user */
        $user = $this->userRepository->get($userId);
        // Поиск ошибок по учету достижения и мероприятиям
        if (Yii::$app->rubac->checkPermission($userId, 'get_achieve_errors')) {
            // Находим ID мероприятий в соответствии с отделом пользователя
            if (Yii::$app->rubac->checkPermission($userId, 'merge_participants')) {
                // Это просто проверка на то, что пользователь обладает возможностями суперконтроллера или админа
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
            $errorsEvent = $this->errorsRepository->getErrorsByTableRows(EventWork::tableName(), $eventIds);
            $errorsForeignEvent = $this->errorsRepository->getErrorsByTableRows(ForeignEventWork::tableName(), $foreignEventIds);

            return array_merge($errorsEvent, $errorsForeignEvent);
        }
    }

}