<?php

namespace frontend\facades;

use frontend\models\work\event\ForeignEventWork;
use frontend\models\work\order\OrderEventWork;
use frontend\services\act_participant\ActParticipantService;
use frontend\services\order\DocumentOrderService;
use frontend\services\order\OrderMainService;
use frontend\services\team\TeamService;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\event\ForeignEventRepository;
use common\repositories\general\OrderPeopleRepository;
use common\repositories\general\PeopleStampRepository;
use common\repositories\order\OrderEventRepository;
use frontend\forms\OrderEventForm;
use frontend\models\forms\ActParticipantForm;
use yii\helpers\ArrayHelper;

class OrderEventFacade
{
    private OrderEventRepository $orderEventRepository;
    private PeopleRepository $peopleRepository;
    private PeopleStampRepository $peopleStampRepository;
    private ForeignEventRepository $foreignEventRepository;
    private OrderMainService $orderMainService;
    private ActParticipantService $actParticipantService;
    private TeamService $teamService;
    private ActParticipantRepository $actParticipantRepository;
    private OrderPeopleRepository $orderPeopleRepository;
    private DocumentOrderService $documentOrderService;
    public function __construct(
        OrderEventRepository $orderEventRepository,
        PeopleRepository $peopleRepository,
        PeopleStampRepository $peopleStampRepository,
        ForeignEventRepository $foreignEventRepository,
        OrderMainService $orderMainService,
        ActParticipantService $actParticipantService,
        TeamService $teamService,
        ActParticipantRepository $actParticipantRepository,
        OrderPeopleRepository $orderPeopleRepository,
        DocumentOrderService $documentOrderService
    ){
        $this->orderEventRepository = $orderEventRepository;
        $this->peopleRepository = $peopleRepository;
        $this->peopleStampRepository = $peopleStampRepository;
        $this->foreignEventRepository = $foreignEventRepository;
        $this->orderMainService = $orderMainService;
        $this->actParticipantService = $actParticipantService;
        $this->teamService = $teamService;
        $this->actParticipantRepository = $actParticipantRepository;
        $this->orderPeopleRepository = $orderPeopleRepository;
        $this->documentOrderService = $documentOrderService;
    }
    public function prepareOrderEventUpdateFacade($id){
        /* @var OrderEventWork $modelOrderEvent */
        /* @var ForeignEventWork $modelForeignEvent */
        /* @var OrderEventForm $model */
        $modelOrderEvent = $this->orderEventRepository->get($id);
        $people = $this->peopleStampRepository->getAll();
        $modelForeignEvent = $this->foreignEventRepository->getByDocOrderId($modelOrderEvent->id);
        $modelActForms = [new ActParticipantForm];
        $model = OrderEventForm::fill($modelOrderEvent, $modelForeignEvent);
        $tables = $this->documentOrderService->getUploadedFilesTables($modelOrderEvent);
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