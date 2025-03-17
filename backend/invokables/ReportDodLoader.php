<?php

namespace backend\invokables;

use common\helpers\common\HeaderWizard;
use common\helpers\files\FilePaths;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;

class ReportDodLoader
{
    /**
     * @var string $templatePath часть пути к шаблону отчета
     * @var string $filename название итогового файла с отчетом
     * @var array $data данные для отчета ДОД
     */
    private string $templatePath;
    private string $filename;
    private array $data;

    public function __construct(
        string $templatePath,
        string $filename,
        array $data
    )
    {
        $this->templatePath = $templatePath;
        $this->filename = $filename;
        $this->data = $data;
    }

    public function __invoke()
    {
        $inputData = IOFactory::load(Yii::$app->basePath . FilePaths::REPORT_TEMPLATES . $this->templatePath);

        $this->setSection3($inputData, $this->data['section3']);
        $this->setSection4($inputData, $this->data['section4']);
        $this->setSection5($inputData, $this->data['section5']);
        $this->setSection10($inputData, $this->data['section10']);
        $this->setSection11($inputData, $this->data['section11']);

        HeaderWizard::setDodReportHeaders($this->filename);
        $writer = new Xlsx($inputData);
        $writer->save('php://output');
        exit;
    }

    public function setSection3(Spreadsheet $inputData, array $data)
    {
        $inputData->getSheet(0)->setCellValue('C8', $data['tech']['all']);
        $inputData->getSheet(0)->setCellValue('C9', $data['science']['all']);
        $inputData->getSheet(0)->setCellValue('C11', $data['social']['all']);
        $inputData->getSheet(0)->setCellValue('C13', $data['art']['all']);
        $inputData->getSheet(0)->setCellValue('C14', $data['sport']['all']);

        $inputData->getSheet(0)->setCellValue('D8', $data['tech']['female']);
        $inputData->getSheet(0)->setCellValue('D9', $data['science']['female']);
        $inputData->getSheet(0)->setCellValue('D11', $data['social']['female']);
        $inputData->getSheet(0)->setCellValue('D13', $data['art']['female']);
        $inputData->getSheet(0)->setCellValue('D14', $data['sport']['female']);

        $inputData->getSheet(0)->setCellValue('E8', $data['tech']['network']);
        $inputData->getSheet(0)->setCellValue('E9', $data['science']['network']);
        $inputData->getSheet(0)->setCellValue('E11', $data['social']['network']);
        $inputData->getSheet(0)->setCellValue('E13', $data['art']['network']);
        $inputData->getSheet(0)->setCellValue('E14', $data['sport']['network']);

        $inputData->getSheet(0)->setCellValue('F8', $data['tech']['remote']);
        $inputData->getSheet(0)->setCellValue('F9', $data['science']['remote']);
        $inputData->getSheet(0)->setCellValue('F11', $data['social']['remote']);
        $inputData->getSheet(0)->setCellValue('F13', $data['art']['remote']);
        $inputData->getSheet(0)->setCellValue('F14', $data['sport']['remote']);
    }

    public function setSection4(Spreadsheet $inputData, array $data)
    {
        $techAlphaIndex = 6;
        $scienceAlphaIndex = 7;
        $socialAlphaIndex = 9;
        $artAlphaIndex = 11;
        $sportAlphaIndex = 12;
        $summaryAlphaIndex = 16;

        $inputData->getSheet(1)->setCellValue('C' . $techAlphaIndex, $data['tech']['all']);
        $inputData->getSheet(1)->setCellValue('C' . $scienceAlphaIndex, $data['science']['all']);
        $inputData->getSheet(1)->setCellValue('C' . $socialAlphaIndex, $data['social']['all']);
        $inputData->getSheet(1)->setCellValue('C' . $artAlphaIndex, $data['art']['all']);
        $inputData->getSheet(1)->setCellValue('C' . $sportAlphaIndex, $data['sport']['all']);
        $inputData->getSheet(1)->setCellValue('C' . $summaryAlphaIndex, $data['summary']['all']);

        $this->fillAgeLine($inputData, $data['tech']['ages'], $techAlphaIndex);
        $this->fillAgeLine($inputData, $data['science']['ages'], $scienceAlphaIndex);
        $this->fillAgeLine($inputData, $data['social']['ages'], $socialAlphaIndex);
        $this->fillAgeLine($inputData, $data['art']['ages'], $artAlphaIndex);
        $this->fillAgeLine($inputData, $data['sport']['ages'], $sportAlphaIndex);
        $this->fillAgeLine($inputData, $data['summary']['ages'], $summaryAlphaIndex);
    }

    public function setSection5(Spreadsheet $inputData, array $data)
    {
        $inputData->getSheet(2)->setCellValue('D6', $data['tech']['budget']);
        $inputData->getSheet(2)->setCellValue('D7', $data['science']['budget']);
        $inputData->getSheet(2)->setCellValue('D9', $data['social']['budget']);
        $inputData->getSheet(2)->setCellValue('D11', $data['art']['budget']);
        $inputData->getSheet(2)->setCellValue('D12', $data['sport']['budget']);

        $inputData->getSheet(2)->setCellValue('F6', $data['tech']['commerce']);
        $inputData->getSheet(2)->setCellValue('F7', $data['science']['commerce']);
        $inputData->getSheet(2)->setCellValue('F9', $data['social']['commerce']);
        $inputData->getSheet(2)->setCellValue('F11', $data['art']['commerce']);
        $inputData->getSheet(2)->setCellValue('F12', $data['sport']['commerce']);
    }

    public function setSection10(Spreadsheet $inputData, array $data)
    {

    }

    public function setSection11(Spreadsheet $inputData, array $data)
    {
        $inputData->getSheet(4)->setCellValue('C8', $data['all']);
        $inputData->getSheet(4)->setCellValue('C9', $data['educational']);

        $inputData->getSheet(4)->setCellValue('F8', $data['all_owner']);
        $inputData->getSheet(4)->setCellValue('F9', $data['educational_owner']);

        $inputData->getSheet(4)->setCellValue('G8', $data['all_rent']);
        $inputData->getSheet(4)->setCellValue('G9', $data['educational_rent']);
    }

    private function fillAgeLine(Spreadsheet $inputData, array $ages, int $rowIndex)
    {
        $counter = 4;
        foreach ($ages as $age) {
            $inputData->getSheet(1)->setCellValue(
                Coordinate::stringFromColumnIndex($counter) . $rowIndex,
                $age
            );
            $counter++;
        }
    }
}