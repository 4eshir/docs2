<?php

namespace frontend\controllers\event;

use common\Model;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\order\OrderEventRepository;
use frontend\forms\event\ForeignEventForm;
use frontend\models\search\SearchForeignEvent;
use frontend\models\work\event\ParticipantAchievementWork;
use frontend\models\work\general\PeopleWork;
use frontend\services\event\ForeignEventService;
use Yii;
use yii\web\Controller;

class ForeignEventController extends Controller
{
    private ForeignEventService $service;
    private OrderEventRepository $orderEventRepository;
    private PeopleRepository $peopleRepository;

    public function __construct(
        $id,
        $module,
        ForeignEventService $service,
        OrderEventRepository $orderEventRepository,
        PeopleRepository $peopleRepository,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
        $this->orderEventRepository = $orderEventRepository;
        $this->peopleRepository = $peopleRepository;
    }

    public function actionIndex()
    {
        $searchModel = new SearchForeignEvent();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate($id)
    {
        $form = new ForeignEventForm($id);

        if ($form->load(Yii::$app->request->post())) {
            $modelAchievements = Model::createMultiple(ParticipantAchievementWork::classname());
            Model::loadMultiple($modelAchievements, Yii::$app->request->post());
            if (Model::validateMultiple($modelAchievements, ['act_participant_id', 'achievement', 'cert_number', 'date'])) {
                $form->newAchievements = $modelAchievements;
            }
            $this->service->attachAchievement($form);
            $this->service->getFilesInstances($form);
            $this->service->saveAchievementFileFromModel($form);

            $form->releaseEvents();
            // что-то делаем с данными
            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('update', [
            'model' => $form,
            'peoples' => $this->peopleRepository->getAll(),
            'orders6' => $this->orderEventRepository->getEventOrdersByLastTime(date('Y-m-d', strtotime($form->startDate . '-6 month'))),
            'orders9' => $this->orderEventRepository->getEventOrdersByLastTime(date('Y-m-d', strtotime($form->startDate . '-9 month'))),
            'modelAchievements' => [new ParticipantAchievementWork],
        ]);
    }

    public function actionView($id)
    {
        $form = new ForeignEventForm($id);
        return $this->render('view',[
            'model' => $form
        ]);
    }
}