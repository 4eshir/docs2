<?php

namespace backend\services\report;

use backend\repositories\report\ParticipantReportRepository;
use common\repositories\event\ForeignEventRepository;
use frontend\models\work\event\ParticipantAchievementWork;
use Yii;
use yii\helpers\ArrayHelper;

class ReportForeignEventService
{
    private ForeignEventRepository $repository;
    private ParticipantReportRepository $participantReportRepository;
    private DebugReportService $debugService;

    public function __construct(
        ForeignEventRepository $repository,
        ParticipantReportRepository $participantReportRepository,
        DebugReportService $debugService
    )
    {
        $this->repository = $repository;
        $this->participantReportRepository = $participantReportRepository;
        $this->debugService = $debugService;
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

        $actsQuery = $this->participantReportRepository->query();
        $actsQuery = $this->participantReportRepository->filterByEvents($actsQuery, ArrayHelper::getColumn($events, 'id'));
        $actsQuery = $this->participantReportRepository->joinWith($actsQuery, 'foreignEventWork');
        $actsQuery = $this->participantReportRepository->joinWith($actsQuery, 'actParticipantBranchWork');
        $actsQuery = $this->participantReportRepository->joinWith($actsQuery, 'participantAchievementWork');
        $actsQuery = $this->participantReportRepository->filterByBranches($actsQuery, $branches);
        $actsQuery = $this->participantReportRepository->filterByFocuses($actsQuery, $focuses);
        $actsQuery = $this->participantReportRepository->filterByAllowRemote($actsQuery, $allowRemotes);

        $result = [];
        $tempSumPart = 0;
        $tempSumAchieve = 0;
        foreach ($levels as $level) {
            $participantQuery = $this->participantReportRepository->filterByEventLevels(clone $actsQuery, [$level]);
            $prizeQuery = $this->participantReportRepository->filterByPrizes(clone $participantQuery, [ParticipantAchievementWork::TYPE_PRIZE]);
            $wineQuery = $this->participantReportRepository->filterByPrizes(clone $participantQuery, [ParticipantAchievementWork::TYPE_WINNER]);

            $result['levels'][$level] = [
                'participant' => count($this->participantReportRepository->getAll($participantQuery)),
                'winners' => count($this->participantReportRepository->getAll($wineQuery)),
                'prizes' => count($this->participantReportRepository->getAll($prizeQuery))
            ];

            if (in_array($level, Yii::$app->eventLevel->getReportLevels())) {
                $tempSumPart += count($this->participantReportRepository->getAll($participantQuery));
                $tempSumAchieve +=
                    count($this->participantReportRepository->getAll($wineQuery)) +
                    count($this->participantReportRepository->getAll($prizeQuery));
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