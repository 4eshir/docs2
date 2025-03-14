<?php

namespace backend\services\report\form;

use backend\builders\GroupParticipantReportBuilder;
use backend\builders\ParticipantReportBuilder;
use backend\builders\TrainingGroupReportBuilder;
use common\components\dictionaries\base\AllowRemoteDictionary;
use common\components\dictionaries\base\FocusDictionary;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

class DodReportService
{
    private TrainingGroupRepository $groupRepository;
    private TrainingGroupParticipantRepository $participantRepository;
    private TrainingGroupReportBuilder $groupBuilder;
    private GroupParticipantReportBuilder $participantBuilder;

    public function __construct(
        TrainingGroupRepository $groupRepository,
        TrainingGroupParticipantRepository $participantRepository,
        TrainingGroupReportBuilder $groupBuilder,
        GroupParticipantReportBuilder $participantBuilder
    )
    {
        $this->groupRepository = $groupRepository;
        $this->participantRepository = $participantRepository;
        $this->groupBuilder = $groupBuilder;
        $this->participantBuilder = $participantBuilder;
    }

    /**
     * Функция-фасад для подсчета всех данных для Раздела 3 ДОД
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function fillSection3(string $startDate, string $endDate) : array
    {
        $result = [];
        // Предварительная подготовка общей части запроса для получения списка групп
        $groupQueries = $this->createGroupQuery($startDate, $endDate);

        $result['tech'] = $this->calculateParticipantsSection3($groupQueries, FocusDictionary::TECHNICAL);
        $result['science'] = $this->calculateParticipantsSection3($groupQueries, FocusDictionary::SCIENCE);
        $result['social'] = $this->calculateParticipantsSection3($groupQueries, FocusDictionary::SOCIAL);
        $result['art'] = $this->calculateParticipantsSection3($groupQueries, FocusDictionary::ART);
        $result['sport'] = $this->calculateParticipantsSection3($groupQueries, FocusDictionary::SPORT);

        return $result;
    }

    /**
     * Основной метод расчета количества обучающихся в Разделе 3
     *
     * @param array $groupQuery
     * @param int $focus
     * @return array
     */
    public function calculateParticipantsSection3(array $groupQuery, int $focus)
    {
        $queryAll = $this->groupBuilder->filterGroupsByFocuses(clone $groupQuery['all'], [$focus]); // все группы
        $queryRemote = $this->groupBuilder->filterGroupsByFocuses(clone $groupQuery['remote'], [$focus]); // только с дистантом
        $queryNetwork = $this->groupBuilder->filterGroupsByFocuses(clone $groupQuery['network'], [$focus]); // только сетевые

        $groupsAll = $this->groupRepository->findAll($queryAll);
        $groupsRemote = $this->groupRepository->findAll($queryRemote);
        $groupsNetwork = $this->groupRepository->findAll($queryNetwork);

        $participantsQueries = $this->createParticipantQuery($groupsAll); // готовые запросы по полу обучающихся (все группы)
        $participantsRemoteQueries = $this->createParticipantQuery($groupsRemote); // готовые запросы по полу обучающихся (дистант группы)
        $participantsNetworkQueries = $this->createParticipantQuery($groupsNetwork); // готовые запросы по полу обучающихся (сетевые группы)

        $participantsAll = $this->participantRepository->findAll($participantsQueries['all']); // все обучающиеся со всех групп
        $participantsFemale = $this->participantRepository->findAll($participantsQueries['female']); // только девочки со всех групп
        $participantsNetworkAll = $this->participantRepository->findAll($participantsNetworkQueries['all']); // все обучающиеся из сетевых групп
        $participantsRemoteAll = $this->participantRepository->findAll($participantsRemoteQueries['all']); // все обучающиеся с дистант групп

        return [
            'all' => count(array_unique(ArrayHelper::getColumn($participantsAll, 'participant_id'))),
            'female' => count(array_unique(ArrayHelper::getColumn($participantsFemale, 'participant_id'))),
            'network' => count(array_unique(ArrayHelper::getColumn($participantsNetworkAll, 'participant_id'))),
            'remote' => count(array_unique(ArrayHelper::getColumn($participantsRemoteAll, 'participant_id'))),
        ];
    }

    /**
     * Запросы на получение всех подходящих учебных групп
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    private function createGroupQuery(string $startDate, string $endDate)
    {
        $query = $this->groupBuilder->query();
        $query = $this->groupBuilder->filterGroupsByDates($query, $startDate, $endDate);
        $queryPersonal = $this->groupBuilder->filterGroupsByAllowRemote(clone $query, [AllowRemoteDictionary::ONLY_PERSONAL]);
        $queryRemote = $this->groupBuilder->filterGroupsByAllowRemote(clone $query, [AllowRemoteDictionary::PERSONAL_WITH_REMOTE]);
        $queryNetwork = $this->groupBuilder->filterGroupsByNetwork(clone $query, [TrainingGroupWork::IS_NETWORK]);

        return [
            'all' => $query,
            'personal' => $queryPersonal,
            'remote' => $queryRemote,
            'network' => $queryNetwork
        ];
    }

    /**
     * Запросы на получение обучающихся в группах (с разбивкой по полу)
     *
     * @param TrainingGroupWork[] $groups
     * @return array
     */
    private function createParticipantQuery(array $groups) : array
    {
        $query = $this->participantBuilder->query();
        $query = $this->participantBuilder->joinWith($query, 'participantWork');
        $query = $this->participantBuilder->filterByGroups($query, ArrayHelper::getColumn($groups, 'id'));
        $queryAll = $this->participantBuilder->filterBySex(clone $query);
        $queryMale = $this->participantBuilder->filterBySex(clone $query, [PersonInterface::SEX_MALE]);
        $queryFemale = $this->participantBuilder->filterBySex(clone $query, [PersonInterface::SEX_FEMALE]);

        return [
            'all' => $queryAll,
            'male' => $queryMale,
            'female' => $queryFemale
        ];
    }

}