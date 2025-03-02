<?php

namespace backend\tests\unit\report\query;

use backend\forms\report\ManHoursReportForm;
use backend\services\report\mock\ReportManHoursMockService;
use common\components\dictionaries\base\AllowRemoteDictionary;
use common\components\dictionaries\base\BranchDictionary;
use common\components\dictionaries\base\FocusDictionary;
use Yii;

class ManHoursReportTest extends \Codeception\Test\Unit
{
    protected ReportManHoursMockService $manHoursMockService;

    /**
     * @var \frontend\tests\UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
        $this->manHoursMockService = Yii::createObject(
            ReportManHoursMockService::class
        );
    }

    protected function _after()
    {
    }

    // Тестируем создание базовых учебных групп

    /**
     * @dataProvider getManHoursReportData
     */
    public function testManHoursReport(ManHoursReportData $data)
    {
        $this->manHoursMockService->setMockData(
            $data->groups,
            $data->participants,
            $data->themes,
            $data->lessons,
            $data->visits
        );

        var_dump($this->manHoursMockService->calculateManHours(
            '2010-01-01',
            '2010-12-31',
            [BranchDictionary::QUANTORIUM],
            [FocusDictionary::TECHNICAL],
            [AllowRemoteDictionary::ONLY_PERSONAL, AllowRemoteDictionary::PERSONAL_WITH_REMOTE],
            [0, 1],
            ManHoursReportForm::MAN_HOURS_FAIR
        ));die;
    }

    public function getManHoursReportData()
    {
        $data = new ManHoursReportData();

        return [
            [
                $data
            ],
        ];
    }

}