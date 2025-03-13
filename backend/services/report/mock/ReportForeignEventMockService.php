<?php

namespace backend\services\report;

use backend\builders\ParticipantReportBuilder;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\event\ForeignEventRepository;
use frontend\models\work\event\ParticipantAchievementWork;
use Yii;
use yii\helpers\ArrayHelper;

class ReportForeignEventMockService
{
    public array $events;
    public array $acts;
    public array $actsBranch;
    public array $achieves;

    public function __construct(
        array $events = [],
        array $acts = [],
        array $actsBranch = [],
        array $achieves = []
    )
    {
        $this->events = $events;
        $this->acts = $acts;
        $this->actsBranch = $actsBranch;
        $this->achieves = $achieves;
    }

    public function calculateEventParticipants(
        string $startDate,
        string $endDate,
        array $branches,
        array $focuses,
        array $allowRemotes,
        array $levels = [],
        int $mode = ReportFacade::MODE_PURE
    )
    {
        $events = $this->repository->getByDatesAndLevels($startDate, $endDate, $levels);

        $actsQuery = $this->builder->query();
        $actsQuery = $this->builder->filterByEvents($actsQuery, ArrayHelper::getColumn($events, 'id'));
        $actsQuery = $this->builder->joinWith($actsQuery, 'foreignEventWork');
        $actsQuery = $this->builder->joinWith($actsQuery, 'actParticipantBranchWork');
        $actsQuery = $this->builder->joinWith($actsQuery, 'participantAchievementWork');
        $actsQuery = $this->builder->filterByBranches($actsQuery, $branches);
        $actsQuery = $this->builder->filterByFocuses($actsQuery, $focuses);
        $actsQuery = $this->builder->filterByAllowRemote($actsQuery, $allowRemotes);

        $result = [];
        $tempSumPart = 0;
        $tempSumAchieve = 0;
        foreach ($levels as $level) {
            $participantQuery = $this->builder->filterByEventLevels(clone $actsQuery, [$level]);
            $prizeQuery = $this->builder->filterByPrizes(clone $participantQuery, [ParticipantAchievementWork::TYPE_PRIZE]);
            $winQuery = $this->builder->filterByPrizes(clone $participantQuery, [ParticipantAchievementWork::TYPE_WINNER]);

            $result['levels'][$level] = [
                'participant' => count($this->actRepository->findAll($participantQuery)),
                'winners' => count($this->actRepository->findAll($winQuery)),
                'prizes' => count($this->actRepository->findAll($prizeQuery))
            ];

            if (in_array($level, Yii::$app->eventLevel->getReportLevels())) {
                $tempSumPart += count($this->actRepository->findAll($participantQuery));
                $tempSumAchieve +=
                    count($this->actRepository->findAll($winQuery)) +
                    count($this->actRepository->findAll($prizeQuery));
            }
        }

        $result['percent'] = $tempSumAchieve / $tempSumPart;

        return [
            'result' => $result,
            'debugData' => $mode == ReportFacade::MODE_DEBUG ?
                $this->debugService->createEventDebugData($events) :
                ''
        ];
    }
}