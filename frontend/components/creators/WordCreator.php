<?php

namespace frontend\components\creators;

use common\components\dictionaries\base\BranchDictionary;
use common\components\wizards\WordWizard;
use common\helpers\common\BaseFunctions;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\educational\training_group\TrainingGroupExpertWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use PhpOffice\PhpWord\PhpWord;
use Yii;

class WordCreator
{
    /**
     * @param TrainingGroupWork $modelGroup
     * @param TrainingGroupParticipantWork[] $groupParticipants
     * @param TrainingGroupExpertWork[] $experts
     * @param string $eventName
     * @return PhpWord
     */
    public static function createProtocol(TrainingGroupWork $modelGroup, array $groupParticipants, array $experts, string $eventName) : PhpWord
    {
        $inputData = new PhpWord();
        $inputData->setDefaultFontName('Times New Roman');
        $inputData->setDefaultFontSize(14);

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));

        $section->addText('ПРОТОКОЛ ИТОГОВОЙ АТТЕСТАЦИИ', array('bold' => true), array('align' => 'center'));
        $section->addText('отдел «'. Yii::$app->branches->get($modelGroup->branch) .'» ГАОУ АО ДО «РШТ»', array('underline' => 'single'), array('align' => 'center'));
        $section->addTextBreak(1);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(4000);
        $cell->addText('«' . date("d", strtotime($modelGroup->protection_date)) . '» '
            . BaseFunctions::monthFromNumbToString(date("m", strtotime($modelGroup->protection_date))) . ' '
            . date("Y", strtotime($modelGroup->protection_date)) . ' г.');
        $cell = $table->addCell(6000);
        $cell->addText('№ ' . $modelGroup->number, null, array('align' => 'right'));
        $section->addTextBreak(2);

        $section->addText('Демонстрация результатов образовательной деятельности', array('bold' => true), array('align' => 'center'));
        $section->addTextBreak(1);
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(5000);
        $cell->addText($modelGroup->trainingProgram->name, array('underline' => 'single'));
        $table->addCell(2000);
        $table->addRow();
        $cell = $table->addCell(5000);
        $cell->addText($modelGroup->number, array('underline' => 'single'));
        $table->addCell(2000);
        $section->addTextBreak(2);

        switch (Yii::$app->branches->get($modelGroup->branch)) {
            case BranchDictionary::QUANTORIUM:
                $boss = 'Цырульников Евгений Сергеевич';
                $bossShort = 'Цырульников Е.С.';
                $expertExept = 19;
                break;
            case BranchDictionary::TECHNOPARK:
                $boss = 'Толочина Оксана Георгиевна';
                $bossShort = 'Толочина О.Г.';
                $expertExept = 946;
                break;
            case BranchDictionary::CDNTT:
                $boss = 'Дубовская Лариса Валерьевна';
                $bossShort = 'Дубовская Л.В.';
                $expertExept = 21;
                break;
            case BranchDictionary::COD:
                $boss = 'Баганина Анна Александровна';
                $bossShort = 'Баганина А.А.';
                $expertExept = 36;
                break;
            default:
                $boss = 'Толочина Оксана Георгиевна';
                $bossShort = 'Толочина О.Г.';
                $expertExept = 946;
        }

        $section->addText('Присутствовали ответственные лица:', null, array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('          1. Руководитель учебной группы – ' . $modelGroup->teachersWork[0]->teacherWork->getFIO(PersonInterface::FIO_FULL) . '.', null, array('align' => 'both', 'spaceAfter' => 0));
        if (Yii::$app->branches->get($modelGroup->branch) === BranchDictionary::MOBILE_QUANTUM) {
            $section->addText('          2. Заместитель руководителя - заведующий по образовательной деятельности ' . $boss . '.', null, array('align' => 'both', 'spaceAfter' => 0));
        }
        else {
            $section->addText('          2. Руководитель отдела «'.Yii::$app->branches->get($modelGroup->branch).'» ' . $boss . '.', null, array('align' => 'both', 'spaceAfter' => 0));
        }

        $numberStr = 3;
        foreach ($experts as $expert) {
            if ($expert->expert_id !== $expertExept) {
                $section->addText('          '.$numberStr.'. ' . $expert->expertWork->positionWork->name . ' ' . $expert->expertWork->getFIO(PersonInterface::FIO_FULL) . '.',null, array('align' => 'both', 'spaceAfter' => 0));
                $numberStr++;
            }
        }
        $section->addTextBreak(1);
        $section->addText($eventName, array('underline' => 'single'), array('spaceAfter' => 0));
        $section->addText('(публичное мероприятие, на котором проводилась аттестация)', array('size' => 12, 'italic' => true), array('spaceAfter' => 0));
        $section->addTextBreak(1);

        $expertFlag = false;
        if ($modelGroup->expertsWork) {
            $numberStr = 1;
            foreach ($modelGroup->expertsWork as $expert) {
                if ($expert->expert_type == TrainingGroupExpertWork::TYPE_EXTERNAL && $expert->expert_id !== $expertExept) {
                    if ($numberStr === 1) {
                        $expertFlag = true;
                        $section->addText('Приглашенные эксперты:', array('underline' => 'single'), array('spaceAfter' => 0));
                    }
                    $section->addText('          '.$numberStr.'. ' . $expert->expertWork->companyWork->short_name . ' ' . $expert->expertWork->positionWork->name . ' ' . $expert->expertWork->getFIO(PersonInterface::FIO_FULL),null, array('align' => 'both', 'spaceAfter' => 0));
                    $numberStr++;
                }
            }
        }
        $section->addTextBreak(1);

        $section->addText('Повестка дня:', null, array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('          1. Принятие решения о результатах итоговой аттестации.', null, array('align' => 'both', 'spaceAfter' => 0));
        $section->addTextBreak(1);
        $section->addText('Приняли участие в итоговой аттестации обучающиеся согласно Приложению № 1 к настоящему протоколу.', null, array('align' => 'both', 'spaceAfter' => 0, 'indentation' => array('hanging' => -700)));
        $section->addTextBreak(1);
        if ($modelGroup->trainingGroupExperts && $expertFlag) {
            $section->addText('Ответственными лицами и экспертами были заданы вопросы.', null, array('align' => 'both', 'spaceAfter' => 0, 'indentation' => array('hanging' => -700)));
        }
        else {
            $section->addText('Ответственными лицами были заданы вопросы.', null, array('align' => 'both', 'spaceAfter' => 0, 'indentation' => array('hanging' => -700)));
        }
        $section->addText('Ответственные лица, ознакомившись с демонстрацией результатов образовательной деятельности каждого обучающегося,', null, array('align' => 'both', 'spaceAfter' => 0, 'indentation' => array('hanging' => -700)));
        $section->addText('Постановили:', array('bold' => true), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('          1. Признать обучающихся согласно Приложению № 2 к настоящему протоколу успешно прошедшими итоговую аттестацию и выдать сертификаты об обучении.', null, array('align' => 'both', 'spaceAfter' => 0));

        $refPart = 0;
        foreach ($groupParticipants as $part) {
            if ($part->certificateWork) {
                $refPart++;
                if ($refPart > 1) {
                    break;
                }
            }
        }

        if ($refPart !== 0) {
            if ($refPart > 1) {
                $section->addText('          1.1. Признать обучающихся согласно Приложению № 3 к настоящему протоколу непрошедшими итоговую аттестацию и выдать справки об обучении.', null, array('align' => 'both', 'spaceAfter' => 0));
            }
            else {
                $section->addText('          1.1. Признать обучающегося согласно Приложению № 3 к настоящему протоколу непрошедшим итоговую аттестацию и выдать справку об обучении.', null, array('align' => 'both', 'spaceAfter' => 0));
            }
            $section->addText('          2. Рекомендовать обучающимся согласно Приложению № 3 к настоящему протоколу повторно пройти итоговую аттестацию.', null, array('align' => 'both', 'spaceAfter' => 0));
        }

        $section->addTextBreak(1);
        $section->addText('Подписи ответственных лиц:');
        $section->addTextBreak(1);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(8000);
        $cell->addText('Руководитель учебной группы');
        $cell = $table->addCell(6000);
        $cell->addText('________________', null, array('align' => 'center'));
        $cell = $table->addCell(6000);
        $cell->addText('/ '.$modelGroup->teachersWork[0]->teacherWork->getFIO(PersonInterface::FIO_SURNAME_INITIALS) . '/', null, array('align' => 'right'));
        $table->addRow();
        $cell = $table->addCell(8000);
        $cell->addText('Руководитель отдела «'.Yii::$app->branches->get($modelGroup->branch).'»');
        $cell = $table->addCell(6000);
        $cell->addText('________________', null, array('align' => 'center'));
        $cell = $table->addCell(6000);
        $cell->addText('/ '. $bossShort . '/', null, array('align' => 'right'));

        foreach ($experts as $expert) {
            if ($expert->expert_id !== $expertExept) {
                $table->addRow();
                $cell = $table->addCell(8000);
                $cell->addText($expert->expertWork->positionWork->name);
                $cell = $table->addCell(6000);
                $cell->addText('________________', null, array('align' => 'center'));
                $cell = $table->addCell(6000);
                $cell->addText('/ '. $expert->expertWork->getFIO(PersonInterface::FIO_SURNAME_INITIALS) . '/', null, array('align' => 'right'));
            }
        }

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15)));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('Приложение №1', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('к протоколу итоговой аттестации', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('«' . date("d", strtotime($modelGroup->protection_date)) . '» '
            . BaseFunctions::monthFromNumbToString(date("m", strtotime($modelGroup->protection_date))) . ' '
            . date("Y", strtotime($modelGroup->protection_date)) . ' г.', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('№ ' . $modelGroup->number, array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $section->addTextBreak(2);

        $section->addText('Перечень обучающихся, принявших участие в итоговой аттестации', null, array('align' => 'center', 'spaceAfter' => 0));
        $section->addTextBreak(1);
        $numberStr = 1;
        foreach ($groupParticipants as $part) {
            $section->addText($numberStr.' '.$part->participantWork->getFIO(PersonInterface::FIO_FULL), null, array('spaceAfter' => 0, 'indentation' => array('hanging' => -700)));
            $numberStr++;
        }

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15)));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('Приложение №2', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('к протоколу итоговой аттестации', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('«' . date("d", strtotime($modelGroup->protection_date)) . '» '
            . BaseFunctions::monthFromNumbToString(date("m", strtotime($modelGroup->protection_date))) . ' '
            . date("Y", strtotime($modelGroup->protection_date)) . ' г.', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('№ ' . $modelGroup->number, array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $section->addTextBreak(2);

        $section->addText('Перечень обучающихся, прошедших итоговую аттестацию', null, array('align' => 'center', 'spaceAfter' => 0));
        $section->addTextBreak(1);
        $numberStr = 1;
        $isAnnex3 = false;
        foreach ($groupParticipants as $part) {
            if ($part->certificateWork->certificate_number !== NULL) {
                $section->addText($numberStr.' '.$part->participantWork->getFIO(PersonInterface::FIO_FULL), null, array('spaceAfter' => 0, 'indentation' => array('hanging' => -700)));
                $numberStr++;
            }
            else {
                $isAnnex3 = true;
            }
        }

        if ($isAnnex3) {
            $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
                'marginLeft' => WordWizard::convertMillimetersToTwips(30),
                'marginBottom' => WordWizard::convertMillimetersToTwips(20),
                'marginRight' => WordWizard::convertMillimetersToTwips(15)));
            $table = $section->addTable();
            $table->addRow();
            $cell = $table->addCell(10000);
            $cell->addText('', null, array('spaceAfter' => 0));
            $cell = $table->addCell(8000);
            $cell->addText('Приложение №3', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
            $table->addRow();
            $cell = $table->addCell(10000);
            $cell->addText('', null, array('spaceAfter' => 0));
            $cell = $table->addCell(8000);
            $cell->addText('к протоколу итоговой аттестации', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
            $table->addRow();
            $cell = $table->addCell(10000);
            $cell->addText('', null, array('spaceAfter' => 0));
            $cell = $table->addCell(8000);
            $cell->addText('«' . date("d", strtotime($modelGroup->protection_date)) . '» '
                . BaseFunctions::monthFromNumbToString(date("m", strtotime($modelGroup->protection_date))) . ' '
                . date("Y", strtotime($modelGroup->protection_date)) . ' г.', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
            $table->addRow();
            $cell = $table->addCell(10000);
            $cell->addText('', null, array('spaceAfter' => 0));
            $cell = $table->addCell(8000);
            $cell->addText('№ ' . $modelGroup->number, array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
            $section->addTextBreak(2);

            $section->addText('Перечень обучающихся, признанных непрошедшими итоговую аттестацию', null, array('align' => 'center', 'spaceAfter' => 0));
            $section->addTextBreak(1);
            $numberStr = 1;
            foreach ($groupParticipants as $part) {
                if ($part->certificateWork->certificate_number === NULL) {
                    $section->addText($numberStr.' '.$part->participantWork->getFIO(PersonInterface::FIO_FULL), null, array('spaceAfter' => 0, 'indentation' => array('hanging' => -700)));
                    $numberStr++;
                }
            }
        }

        return $inputData;
    }
}