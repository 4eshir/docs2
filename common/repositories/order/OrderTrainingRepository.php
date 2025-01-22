<?php

namespace common\repositories\order;
use app\models\work\order\OrderTrainingWork;
use app\services\order\OrderTrainingService;
use common\components\dictionaries\base\NomenclatureDictionary;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use DomainException;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class OrderTrainingRepository
{
    public TrainingGroupParticipantRepository $trainingGroupParticipantRepository;
    public TrainingGroupRepository $trainingGroupRepository;
    public OrderTrainingService $orderTrainingService;
    public function __construct(
        TrainingGroupParticipantRepository $trainingGroupParticipantRepository,
        TrainingGroupRepository $trainingGroupRepository,
        OrderTrainingService $orderTrainingService
    )
    {
        $this->trainingGroupParticipantRepository = $trainingGroupParticipantRepository;
        $this->trainingGroupRepository = $trainingGroupRepository;
        $this->orderTrainingService = $orderTrainingService;
    }

    public function get($id)
    {
        return OrderTrainingWork::findOne($id);
    }
    public function save(OrderTrainingWork $model)
    {
        if (!$model->save()) {
            throw new DomainException('Ошибка сохранения документа. Проблемы: '.json_encode($model->getErrors()));
        }
        return $model->id;
    }
}