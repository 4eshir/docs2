<?php

namespace backend\services\report;

use backend\forms\report\ForeignEventReportForm;
use backend\forms\report\ManHoursReportForm;
use backend\services\report\interfaces\ForeignEventServiceInterface;
use backend\services\report\interfaces\ManHoursServiceInterface;
use Yii;
use yii\base\InvalidConfigException;

class ReportFacade
{
    // Режим формирования отчета
    const MODE_PURE = 1; // формирование только отчетных данных. работает быстро
    const MODE_DEBUG = 2; // формирование отчетных данных вместе с подробным исходными данными. работает сильно медленнее MODE_PURE

    /**
     * @param ManHoursReportForm $form
     * @return array
     * @throws InvalidConfigException
     */
    public static function generateManHoursReport(ManHoursReportForm $form, ManHoursServiceInterface $service)
    {
        $manHoursResult = [];
        if ($form->isManHours()) {
            $manHoursResult['manHours'] =
                $service->calculateManHours(
                    $form->startDate,
                    $form->endDate,
                    $form->branch,
                    $form->focus,
                    $form->allowRemote,
                    $form->budget,
                    $form->method,
                    $form->teacher !== '' ? [$form->teacher] : [],
                    $form->mode
                );
        }

        if ($form->isParticipants()) {
            array_shift($form->type);
            $manHoursResult['participants'] =
                $service->calculateParticipantsByPeriod(
                    $form->startDate,
                    $form->endDate,
                    $form->branch,
                    $form->focus,
                    $form->allowRemote,
                    $form->budget,
                    $form->type,
                    $form->unic,
                    $form->teacher !== '' ? [$form->teacher] : [],
                    $form->mode
                );
        }

        return $manHoursResult;
    }

    public static function generateParticipantsReport(ForeignEventReportForm $form, ForeignEventServiceInterface $service)
    {
        return $service->calculateEventParticipants(
            $form->startDate,
            $form->endDate,
            $form->branches,
            $form->focuses,
            $form->allowRemotes,
            $form->levels,
            $form->mode
        );
    }
}