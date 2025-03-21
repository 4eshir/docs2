<?php

namespace frontend\components\creators;

use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\VisitRepository;
use frontend\forms\journal\JournalForm;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Yii;

class ExcelCreator
{
    public static function createJournal(int $groupId) : Spreadsheet
    {
        $onPage = 21; //количество занятий на одной строке в листе
        $lesCount = 0; //счетчик для страниц

        $lessons = (Yii::createObject(TrainingGroupLessonRepository::class))->getLessonsFromGroup($groupId);
        $newLessons = array();
        foreach ($lessons as $lesson) {
            $newLessons[] = $lesson->id;
        }
        $visits = (Yii::createObject(VisitRepository::class));
        $visits = VisitWork::find()
            ->joinWith(['foreignEventParticipant foreignEventParticipant'])
            ->joinWith(['trainingGroupLesson trainingGroupLesson'])
            ->where(['in', 'training_group_lesson_id', $newLessons])
            ->orderBy(
                [
                    'foreignEventParticipant.secondname' => SORT_ASC,
                    'foreignEventParticipant.firstname' => SORT_ASC,
                    'trainingGroupLesson.lesson_date' => SORT_ASC,
                    'trainingGroupLesson.id' => SORT_ASC
                ]
            )->all();

        $newVisits = array();
        $newVisitsId = array();
        foreach ($visits as $visit) $newVisits[] = $visit->status;
        foreach ($visits as $visit) $newVisitsId[] = $visit->id;
        $model->visits = $newVisits;
        $model->visits_id = $newVisitsId;

        $group = TrainingGroupWork::find()->where(['id' => $training_group_id])->one();
        $parts = \app\models\work\TrainingGroupParticipantWork::find()->joinWith(['participant participant'])->where(['training_group_id' => $model->trainingGroup])->orderBy(['participant.secondname' => SORT_ASC])->all();
        $lessons = \app\models\work\TrainingGroupLessonWork::find()->where(['training_group_id' => $model->trainingGroup])->orderBy(['lesson_date' => SORT_ASC, 'id' => SORT_ASC])->all();

        $flag = 1; // флаг вида журнала, в зависимости от количества детей
        if (count($parts) > 20)
        {
            $fileName = '/templates/electronicJournal2.xlsx';
            $flag = 0;
        }
        else
            $fileName = '/templates/electronicJournal.xlsx';

        $inputType = IOFactory::identify(Yii::$app->basePath . $fileName);
        $reader = IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath . $fileName);

        for ($i = 1; $i < (count($lessons) / ($onPage * (1 + $flag))) * ceil(count($parts) / 46); $i++)
        {
            $clone = clone $inputData->getActiveSheet();
            $clone->setTitle('Шаблон' . $i);
            $inputData->addSheet($clone);
        }

        $magic = 0; //  смещение между страницами за счет фио+подписи и пустых строк
        $sheets = 0;
        while ($lesCount < count($lessons) / $onPage)
        {
            if ($lesCount !== 0 && $lesCount % 2 === 0)
            {
                $sheets++;
                $magic = 0;
            }
            if ($lesCount % 2 !== 0)
            {
                if ($flag == 1)
                    $magic = 26;
                else
                {
                    $sheets++;
                    $magic = 0;
                }
            }

            $inputData->getSheet($sheets)->setCellValueByColumnAndRow(1, 1 + $magic, 'Группа: ' . PHP_EOL . $group->number);
            $inputData->getSheet($sheets)->setCellValueByColumnAndRow(2, 1 + $magic, 'Программа: ' . $group->programNameNoLink);
            $inputData->getSheet($sheets)->getStyle('B'. $magic);

            $tempSheets = $sheets;
            for ($cp = 0; $cp < ceil(count($parts) / 46); $cp++)
            {
                for ($i = 0; $i + $lesCount * $onPage < count($lessons) && $i < $onPage; $i++) //цикл заполнения дат на странице
                {
                    $inputData->getSheet($tempSheets)->getCellByColumnAndRow(2 + $i, 4 + $magic)->setValueExplicit(date("d.m", strtotime($lessons[$i + $lesCount * $onPage]->lesson_date)), \PHPExcel_Cell_DataType::TYPE_STRING);
                    $inputData->getSheet($tempSheets)->getCellByColumnAndRow(2 + $i, 4 + $magic)->getStyle()->getAlignment()->setTextRotation(90);
                }
                $tempSheets++;
            }

            for($i = 0; $i < count($parts); ) //цикл заполнения детей на странице
            {
                if ($i !== 0 && $i % 46 === 0)
                {
                    $sheets++;
                    $inputData->getSheet($sheets)->setCellValueByColumnAndRow(1, 1 + $magic, 'Группа: ' . $group->number);
                    $inputData->getSheet($sheets)->setCellValueByColumnAndRow(2, 1 + $magic, 'Программа: ' . $group->programNameNoLink);
                    $inputData->getSheet($sheets)->getStyle('B'. $magic);
                    $inputData->getSheet($sheets)->getCellByColumnAndRow(2 + $i, 4 + $magic)->setValueExplicit(date("d.m", strtotime($lessons[$i + $lesCount * $onPage]->lesson_date)), \PHPExcel_Cell_DataType::TYPE_STRING);
                    $inputData->getSheet($sheets)->getCellByColumnAndRow(2 + $i, 4 + $magic)->getStyle()->getAlignment()->setTextRotation(90);
                }
                for ($j = 0; $j < 46 && $i < count($parts); $j++)
                {
                    $inputData->getSheet($sheets)->setCellValueByColumnAndRow(1, $j + 6 + $magic, $parts[$i]->participantWork->shortName);
                    $i++;
                }
                //$inputData->getSheet($sheets)->setCellValueByColumnAndRow(1, $i + 6 + $magic, $parts[$i]->participantWork->shortName);
            }
            $lesCount++;
        }


        for ($cp = 0; $cp < count($parts); )
        {
            $sheets = 0;
            $delay = 0;

            if ($cp !== 0 && $cp % 46 === 0)
            {
                $sheets++;
            }

            for ($j = 0; $j < 46 && $cp < count($parts); $j++)
            {
                $magic = 0;
                $tempSheets = $sheets;
                for ($i = 0; $i < count($lessons); $i++, $delay++)
                {
                    $visits = \app\models\work\VisitWork::find()->where(['id' => $model->visits_id[$delay]])->one();

                    if ($i % $onPage === 0 && $i !== 0)
                    {
                        if (($magic === 26 && $flag === 1) || $flag === 0)
                        {
                            $magic = 0;
                            if (count($parts) > 46)
                                $tempSheets = $tempSheets + 2;
                            else
                                $tempSheets++;
                        }
                        else if ($flag === 1)
                            $magic = 26;
                    }
                    $inputData->getSheet($tempSheets)->setCellValueByColumnAndRow(2 + $i % $onPage, 6 + $j + $magic, $visits->excelStatus);
                }
                $cp++;
            }
        }

        for ($sheets = 0; $sheets < $inputData->getSheetCount(); $sheets++)
        {
            $inputData->getSheet($sheets)->setCellValueByColumnAndRow(32, 51, count($lessons)*count($parts));
        }

        $lessons = LessonThemeWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['trainingGroupLesson.training_group_id' => $training_group_id])
            ->orderBy(['trainingGroupLesson.lesson_date' => SORT_ASC, 'trainingGroupLesson.lesson_start_time' => SORT_ASC])->all();

        $sheets = 0;
        for ($i = 0; $i < ceil(count($parts) / 46); $i++)
        {
            $magic = 5;
            $tempSheets = $sheets;
            foreach ($lessons as $lesson)
            {
                $inputData->getSheet($tempSheets)->setCellValueByColumnAndRow(26, $magic, date("d.m.Y", strtotime($lesson->trainingGroupLesson->lesson_date)));
                $inputData->getSheet($tempSheets)->setCellValueByColumnAndRow(27, $magic, truncateString($lesson->theme));
                $magic++;

                if ($magic > 20 * (1 + $flag) + 5 + $flag)
                {
                    if (count($parts) > 46)
                        $tempSheets += 2;
                    else
                        $tempSheets++;
                    if ($tempSheets >= $inputData->getSheetCount())
                    {
                        break;
                    }
                    $magic = 5;
                }
            }
            $sheets++;
        }

        $themes = GroupProjectThemesWork::find()->where(['confirm' => 1])->andWhere(['training_group_id' => $training_group_id])->all();

        $strThemes = 'Тема проекта: ';
        foreach ($themes as $theme)
            $strThemes .= $theme->projectTheme->name.', ';

        $strThemes = substr($strThemes, 0, -2);

        $order1 = DocumentOrderWork::find()->joinWith(['orderGroups orderGroups'])->where(['orderGroups.training_group_id' => $training_group_id])->orderBy(['order_date' => SORT_ASC])->one();
        $order2 = DocumentOrderWork::find()->joinWith(['orderGroups orderGroups'])->where(['orderGroups.training_group_id' => $training_group_id])->andWhere(['study_type' => 0])->orderBy(['order_date' => SORT_ASC])->one();

        for ($sheets = 0; $sheets < $inputData->getSheetCount(); $sheets++)
        {
            if ($order1)
            {
                if ($order1->order_postfix == null)
                    $inputData->getSheet($sheets)->setCellValueByColumnAndRow(26,51, $order1->order_number.'/'.$order1->order_copy_id);
                else
                    $inputData->getSheet($sheets)->setCellValueByColumnAndRow(26, 51, $order1->order_number.'/'.$order1->order_copy_id.'/'.$order1->order_postfix);
            }

            if ($order2)
            {
                if ($order2->order_postfix == null)
                    $inputData->getSheet($sheets)->setCellValueByColumnAndRow(30,51, $order2->order_number.'/'.$order2->order_copy_id);
                else
                    $inputData->getSheet($sheets)->setCellValueByColumnAndRow(30, 51, $order2->order_number.'/'.$order2->order_copy_id.'/'.$order2->order_postfix);
            }

            if ($group->protection_date)
                $inputData->getSheet($sheets)->setCellValue('AB51', date("d.m.Y", strtotime($group->protection_date)));

            $inputData->getSheet($sheets)->setCellValue('Z1', $strThemes);
            $inputData->getSheet($sheets)->getStyle('Z1')->getAlignment()->setWrapText(true);
            $inputData->getSheet($sheets)->getStyle('B1')->getAlignment()->setWrapText(true);
        }

        return $inputData;
    }
}