<?php
namespace frontend\controllers\order;
use app\models\work\order\OrderEventWork;
use common\repositories\dictionaries\PeopleRepository;
use DomainException;
use Yii;
use yii\web\Controller;
class OrderEventController extends Controller
{
    private PeopleRepository $peopleRepository;
    public function __construct(
        $id, $module,
        PeopleRepository $peopleRepository,
        $config = []
    )
    {
        $this->peopleRepository = $peopleRepository;
        parent::__construct($id, $module, $config);
    }
    public function actionIndex() {
    }
    public function actionCreate() {
        $model = new OrderEventWork();
        $people = $this->peopleRepository->getOrderedList();
        $post = Yii::$app->request->post();
        if($model->load($post)) {
            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }
            var_dump($post);
        }
        return $this->render('create', [
            'model' => $model,
            'people' => $people
        ]);
    }
}