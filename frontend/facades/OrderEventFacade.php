<?php

namespace frontend\facades;

use app\models\work\event\ForeignEventWork;
use app\models\work\order\OrderEventWork;
use app\services\act_participant\ActParticipantService;
use app\services\order\OrderMainService;
use app\services\team\TeamService;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\event\ForeignEventRepository;
use common\repositories\general\OrderPeopleRepository;
use common\repositories\order\OrderEventRepository;
use frontend\forms\OrderEventForm;
use frontend\models\forms\ActParticipantForm;
use yii\helpers\ArrayHelper;

class OrderEventFacade
{
    private OrderEventRepository $orderEventRepository;
    private PeopleRepository $peopleRepository;
    private ForeignEventRepository $foreignEventRepository;
    private OrderMainService $orderMainService;
    private ActParticipantService $actParticipantService;
    private TeamService $teamService;
    private ActParticipantRepository $actParticipantRepository;
    private OrderPeopleRepository $orderPeopleRepository;
    public function __construct(
        OrderEventRepository $orderEventRepository,
        PeopleRepository $peopleRepository,
        ForeignEventRepository $foreignEventRepository,
        OrderMainService $orderMainService,
        ActParticipantService $actParticipantService,
        TeamService $teamService,
        ActParticipantRepository $actParticipantRepository,
        OrderPeopleRepository $orderPeopleRepository
    ){
        $this->orderEventRepository = $orderEventRepository;
        $this->peopleRepository = $peopleRepository;
        $this->foreignEventRepository = $foreignEventRepository;
        $this->orderMainService = $orderMainService;
        $this->actParticipantService = $actParticipantService;
        $this->teamService = $teamService;
        $this->actParticipantRepository = $actParticipantRepository;
        $this->orderPeopleRepository = $orderPeopleRepository;
    }
    public function prepareOrderEventUpdateFacade($id){
        /* @var OrderEventWork $modelOrderEvent */
        /* @var ForeignEventWork $modelForeignEvent */
        /* @var OrderEventForm $model */
        $modelOrderEvent = $this->orderEventRepository->get($id);
        $people = $this->peopleRepository->getOrderedList();
        $modelForeignEvent = $this->foreignEventRepository->getByDocOrderId($modelOrderEvent->id);
        $modelActForms = [new ActParticipantForm];
        $model = OrderEventForm::fill($modelOrderEvent, $modelForeignEvent);
        $tables = $this->orderMainService->getUploadedFilesTables($modelOrderEvent);
        $modelResponsiblePeople = $this->orderMainService->getResponsiblePeopleTable($modelOrderEvent->id);
        $actTable = $this->actParticipantService->createActTable($modelForeignEvent->id);
        $nominations = array_unique(ArrayHelper::getColumn($this->actParticipantRepository->getByForeignEventId($modelForeignEvent->id), 'nomination'));
        $teams = $this->teamService->getNamesByForeignEventId($modelForeignEvent->id);
        return [
            'people' => $people,
            'tables' => $tables,
            'actTable' => $actTable,
            'nominations' => $nominations,
            'teams' => $teams,
            'model' => $model,
            'modelActForms' => $modelActForms,
            'modelResponsiblePeople' => $modelResponsiblePeople,
            'modelForeignEvent' => $modelForeignEvent,
            'modelOrderEvent' => $modelOrderEvent
        ];
    }
    public function modelOrderEventFormFacade($model, $id)
    {
        $orderNumber = $model->order_number;
        $responsiblePeople = ArrayHelper::getColumn($this->orderPeopleRepository->getResponsiblePeople($id), 'people_id');
        return [
            'orderNumber' => $orderNumber,
            'responsiblePeople' => $responsiblePeople
        ];
    }
}