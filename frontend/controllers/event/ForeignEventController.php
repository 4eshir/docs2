<?php

namespace frontend\controllers\event;

use common\repositories\dictionaries\PeopleRepository;
use common\repositories\order\OrderEventRepository;
use frontend\forms\event\ForeignEventForm;
use frontend\models\search\SearchForeignEvent;
use frontend\models\work\event\ParticipantAchievementWork;
use frontend\models\work\general\PeopleWork;
use Yii;
use yii\web\Controller;

class ForeignEventController extends Controller
{
    private OrderEventRepository $orderEventRepository;
    private PeopleRepository $peopleRepository;

    public function __construct(
        $id,
        $module,
        OrderEventRepository $orderEventRepository,
        PeopleRepository $peopleRepository,
        $config = [])
    {
        parent::__construct($id, $module, $config);
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