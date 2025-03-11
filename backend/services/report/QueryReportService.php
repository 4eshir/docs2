<?php

namespace backend\services\report;

use common\helpers\common\HeaderWizard;
use common\helpers\creators\ExcelCreator;
use Hidehalo\Nanoid\Client;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Yii;

class QueryReportService
{
    public function downloadCsvDebugFile(string $rawData, array $headers)
    {
        $data = json_decode($rawData, true);
        $writer = new Csv(
            ExcelCreator::createCsvFile(
                $data,
                $headers
            )
        );

        HeaderWizard::setCsvLoadHeaders((Yii::createObject(Client::class))->generateId(10) . '.csv');
        $writer->setDelimiter(';');
        $writer->setOutputEncoding('windows-1251');
        $writer->save('php://output');
        exit;
    }
}