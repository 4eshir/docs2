<?php

namespace common\repositories\educational;

use common\components\dictionaries\base\NomenclatureDictionary;
use common\components\traits\CommonDatabaseFunctions;
use common\repositories\providers\training_group\TrainingGroupProvider;
use common\repositories\providers\training_group\TrainingGroupProviderInterface;
use DomainException;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use Yii;
use yii\helpers\ArrayHelper;

class TrainingGroupRepository
{
    use CommonDatabaseFunctions;

    private $provider;
    private OrderTrainingGroupParticipantRepository $orderTrainingGroupParticipantRepository;
    private TrainingGroupParticipantRepository $trainingGroupParticipantRepository;
    public function __construct(
        TrainingGroupProviderInterface $provider = null,
        OrderTrainingGroupParticipantRepository $orderTrainingGroupParticipantRepository,
        TrainingGroupParticipantRepository $trainingGroupParticipantRepository
    )
    {
        if (!$provider) {
            $provider = Yii::createObject(TrainingGroupProvider::class);
        }

        $this->provider = $provider;
        $this->orderTrainingGroupParticipantRepository = $orderTrainingGroupParticipantRepository;
        $this->trainingGroupParticipantRepository = $trainingGroupParticipantRepository;
    }

    public function get($id)
    {
        return $this->provider->get($id);
    }

    public function getAll()
    {
        return $this->provider->getAll();
    }

    public function getByTeacher($teacherId)
    {
        if (get_class($this->provider) == TrainingGroupProvider::class) {
            return $this->provider->getByTeacher($teacherId);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода getByTeacher');
        }
    }

    public function getGroupsForCertificates()
    {
        return $this->provider->getGroupsForCertificates();
    }

    public function getParticipants($id)
    {
        return $this->provider->getParticipants($id);
    }

    public function getLessons($id)
    {
        return $this->provider->getLessons($id);
    }

    public function getExperts($id)
    {
        return $this->provider->getExperts($id);
    }

    public function getThemes($id)
    {
        return $this->provider->getThemes($id);
    }

    public function save(TrainingGroupWork $group)
    {
        return $this->provider->save($group);
    }

    public function delete(TrainingGroupWork $model)
    {
        return $this->provider->delete($model);
    }

    public function getByBranchQuery($branch)
    {
        if (get_class($this->provider) == TrainingGroupProvider::class) {
            return $this->provider->getByBranchQuery($branch);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода getByBranchQuery');
        }
    }

    public function getByBranches(array $branches)
    {
        if (get_class($this->provider) == TrainingGroupProvider::class) {
            return $this->provider->getByBranch($branches);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода getByBranches');
        }
    }
    public function empty(){
        return TrainingGroupWork::find()->where(['id' => 0]);
    }
    public function getById($id)
    {
        return TrainingGroupWork::findAll(['id' => $id]);
    }
    public function getAttachedGroupsByOrder($orderId, $status){
        if ($status == NomenclatureDictionary::ORDER_ENROLL){
            $participants = ArrayHelper::getColumn($this->orderTrainingGroupParticipantRepository->getByOrderIds($orderId), 'training_group_participant_in_id');
            $groups = array_unique(ArrayHelper::getColumn($this->trainingGroupParticipantRepository->getAll($participants), 'training_group_id'));
        }
        else if ($status == NomenclatureDictionary::ORDER_DEDUCT) {
            $participants = ArrayHelper::getColumn($this->orderTrainingGroupParticipantRepository->getByOrderIds($orderId), 'training_group_participant_out_id');
            $groups = array_unique(ArrayHelper::getColumn($this->trainingGroupParticipantRepository->getAll($participants), 'training_group_id'));
        }
        else if ($status == NomenclatureDictionary::ORDER_TRANSFER) {
            $participants = ArrayHelper::getColumn($this->orderTrainingGroupParticipantRepository->getByOrderIds($orderId), 'training_group_participant_in_id');
            $groups = array_unique(ArrayHelper::getColumn($this->trainingGroupParticipantRepository->getAll($participants), 'training_group_id'));
        }
        return $groups;
    }
}