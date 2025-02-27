<?php

namespace backend\services\report;

use backend\forms\report\ManHoursReportForm;
use Yii;
use yii\base\InvalidConfigException;

class ReportFacade
{
    /**
     * @param ManHoursReportForm $form
     * @return array
     * @throws InvalidConfigException
     */
    public static function generateManHoursReport(ManHoursReportForm $form)
    {
        $service = Yii::createObject(ReportManHoursService::class);
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
}