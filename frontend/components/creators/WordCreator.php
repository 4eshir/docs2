<?php

namespace frontend\components\creators;
use app\models\work\order\OrderEventGenerateWork;
use common\components\dictionaries\base\BranchDictionary;
use common\components\wizards\WordWizard;
use common\helpers\common\BaseFunctions;
use common\models\scaffold\ActParticipantBranch;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\educational\training_group\TrainingGroupExpertWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\event\ForeignEventWork;
use frontend\models\work\general\OrderPeopleWork;
use frontend\models\work\order\DocumentOrderWork;
use frontend\models\work\team\ActParticipantWork;
use frontend\models\work\team\SquadParticipantWork;
use PhpOffice\PhpWord\PhpWord;
use Yii;
use yii\helpers\ArrayHelper;

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
    public static function generateOrderEvent($order_id)
    {
        /* @var $supplement OrderEventGenerateWork */
        /* @var $order DocumentOrderWork */
        /* @var $oneActPart SquadParticipantWork*/
        ini_set('memory_limit', '512M');

        $inputData = new PhpWord();
        $inputData->setDefaultFontName('Times New Roman');
        $inputData->setDefaultFontSize(14);

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));

        $section->addText('Министерство образования и науки Астраханской области', array('lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('государственное автономное образовательное учреждение', array('lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('Астраханской области дополнительного образования', array('lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('«Региональный школьный технопарк»', array('bold' => true, 'lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('ГАОУ АО ДО «РШТ»', array('bold' => true, 'lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('ПРИКАЗ', array('bold' => true, 'lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addTextBreak(2);

        /*----------------*/
        $order = DocumentOrderWork::find()->where(['id' => $order_id])->one();
        $res = OrderPeopleWork::find()->where(['order_id' => $order->id])->all();
        $supplement = OrderEventGenerateWork::find()->where(['order_id' => $order_id])->one();
        $foreignEvent = ForeignEventWork::find()->where(['order_participant_id' => $order_id])->one();
        $acts = ArrayHelper::getColumn(ActParticipantWork::find()->where(['foreign_event_id' => $foreignEvent->id])->all(), 'id');
        $teacherParts = SquadParticipantWork::find()->where(['IN', 'act_participant_id' , $acts])->all();

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('«' . date("d", strtotime($order->order_date)) . '» '
            . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
            . date("Y", strtotime($order->order_date)) . ' г.');
        $cell = $table->addCell(12000);
        $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
        if ($order->order_postfix !== NULL)
            $text .= '/' . $order->order_postfix;
        $cell->addText($text, null, array('align' => 'right'));
        $section->addTextBreak(1);

        $section->addText($order->order_name, null, array('align' => 'both'));
        $section->addTextBreak(1);

        /* переменная цели и соответствия*/
        $purpose = Yii::$app->goals->get($supplement->purpose);
        $invitations = ['', ' и в соответствии с Регламентом', ' и в соответствии с Письмом', ' и в соответствии с Положением'];
        $invitation = $invitations[$supplement->doc_event].' '.$supplement->document_details;
        $section->addText('С целью '.$purpose.$invitation, null, array('align' => 'both', 'indentation' => array('hanging' => -700)));
        $section->addTextBreak(1);
        $section->addText('ПРИКАЗЫВАЮ:', array('lineHeight' => 1.0), array('spaceAfter' => 0));
        $section->addText('1.	Принять участие в мероприятии «'.$foreignEvent->name.'» (далее – мероприятие) и утвердить перечень учащихся, участвующих в мероприятии, и педагогов, ответственных за подготовку и контроль результатов участия в мероприятии, согласно Приложению к настоящему приказу.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('2.	Назначить ответственным за сбор и предоставление информации об участии в мероприятии для внесения в Цифровую систему хранения документов ГАОУ АО ДО «РШТ» (далее – ЦСХД) '.$supplement->respPeopleInfo->getFullFio().'.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('3.	Определить срок предоставления информации об участии в мероприятии: '.$supplement->time_provision_day.' рабочих дней со дня завершения мероприятия.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('4.	Назначить ответственным за внесение информации об участии в мероприятии в ЦСХД '.$supplement->extraRespInsert->getFullFio().'.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('5.	Определить срок для внесения информации об участии в мероприятии: '.$supplement->time_insert_day.' рабочих дней со дня завершения мероприятия.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('6.	Назначить ответственным за методический контроль подготовки учащихся к участию в мероприятии и информационное взаимодействие с организаторами мероприятия '.$supplement->extraRespMethod->getFullFio().'.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('7.	Назначить ответственным за информирование работников о настоящем приказе '.$supplement->extraRespInfoStuff->getFullFio().'.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('8.	Контроль исполнения приказа оставляю за собой.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));

        $section->addTextBreak(2);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Директор');
        $cell = $table->addCell(12000);
        $cell->addText('В.В. Войков', null, array('align' => 'right'));

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Проект вносит:');
        $cell = $table->addCell(12000);
        $cell->addText($order->bring->getFullFio(), null, array('align' => 'right'));
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Исполнитель:');
        $cell = $table->addCell(12000);
        $cell->addText($order->executor->getFullFio(), null, array('align' => 'right'));

        $section->addText('Ознакомлены:');
        $table = $section->addTable();
        for ($i = 0; $i != count($res); $i++, $c++)
        {
            $fio = $res[$i]->people->getFullFio();

            $table->addRow();
            $cell = $table->addCell(8000);
            $cell->addText('«___» __________ 20___ г.');
            $cell = $table->addCell(5000);
            $cell->addText('    ________________/', null, array('align' => 'right'));
            $cell = $table->addCell(5000);
            $cell->addText($fio . '/');
        }

        /*тут перечень учащихся*/
        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(20),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('Приложение', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('к приказу директора', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('ГАОУ АО ДО «РШТ»', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
        if ($order->order_postfix !== NULL)
            $text .= '/' .  $order->order_postfix;
        $cell->addText('от «' . date("d", strtotime($order->order_date)) . '» '
            . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
            . date("Y", strtotime($order->order_date)) . ' г. '
            . $text, array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $cell->addTextBreak(1);
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('УТВЕРЖДАЮ', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('Директор ГАОУ АО ДО «РШТ»', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('_________________ В.В. Войков', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $section->addTextBreak(2);

        $section->addText('Перечень учащихся ГАОУ АО ДО «РШТ» – участников мероприятии', array('bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('«'.$foreignEvent->name.'» –', array('bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('с указанием педагогов, ответственных за подготовку участников и контроль', array('bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('результатов участия', array('bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $section->addTextBreak(1);

        $table = $section->addTable(array('borderColor' => '000000', 'borderSize' => '6'));
        $table->addRow();
        $cell = $table->addCell(1000);
        $cell->addText('<w:br/><w:br/><w:br/>№ п/п', array('size' => '12', 'bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(4000);
        $cell->addText('<w:br/><w:br/><w:br/>Ф.И.О. участника', array('size' => '12', 'bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(3000);
        $cell->addText('Номинация (разряд, трек, класс, и т.п.), в которой производится участие в мероприятии', array('size' => '12', 'bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(3000);
        $cell->addText('Направленность образовательных программ, к которой относится участие в мероприятии', array('size' => '12', 'bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(3000);
        $cell->addText('Отдел ГАОУ АО ДО «РШТ», на базе которого проведена подготовка участника', array('size' => '12', 'bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(4000);
        $cell->addText('Ф.И.О. педагога, ответственного за подготовку участника и контроль результатов его участия', array('size' => '12', 'bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $tBranchs = ActParticipantBranch::find();
        foreach ($teacherParts as $key => $oneActPart)
        {
            $table->addRow();
            $cell = $table->addCell(1000);
            $cell->addText($key+1, array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
            $cell = $table->addCell(4000);
            $cell->addText($oneActPart->participantWork->getFullFio(), array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
            $cell = $table->addCell(3000);
            $cell->addText($oneActPart->actParticipantWork->nomination, array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
            $cell = $table->addCell(3000);
            $cell->addText(Yii::$app->focus->get($oneActPart->actParticipantWork->focus), array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));

            $cell = $table->addCell(3000);
            $branches = $tBranchs->where(['act_participant_id' => $oneActPart->id])->all();
            foreach ($branches as $branch)
                $cell->addText(Yii::$app->branches->get($branch->branch), array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));

            $cell = $table->addCell(4000);
            $cell->addText($oneActPart->actParticipantWork->teacherWork->getFullFio(), array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
            if ($oneActPart->actParticipantWork->teacher2_id != null)
                $cell->addText($oneActPart->actParticipantWork->teacher2Work->getFullFio(), array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));

        }
        $text = 'Пр.' . date("Ymd", strtotime($order->order_date)) . '_' . $order->order_number . $order->order_copy_id . $order->order_postfix . '_' . substr($order->order_name, 0, 35);
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $text . '.docx"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        return $inputData;
    }
    public static function generateOrderTrainingEnroll($orderId)
    {
        ini_set('memory_limit', '512M');

        $inputData = new PhpWord();
        $inputData->setDefaultFontName('Times New Roman');
        $inputData->setDefaultFontSize(14);

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));

        $section->addText('Министерство образования и науки Астраханской области', array('lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('государственное автономное образовательное учреждение', array('lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('Астраханской области дополнительного образования', array('lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('«Региональный школьный технопарк»', array('bold' => true, 'lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('ГАОУ АО ДО «РШТ»', array('bold' => true, 'lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('ПРИКАЗ', array('bold' => true, 'lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addTextBreak(2);


        $text = ' ';
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $text . '.docx"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');

        return $inputData;
    }
    public static function generateOrderTrainingDeduct($orderId)
    {
        ini_set('memory_limit', '512M');

        $inputData = new PhpWord();
        $inputData->setDefaultFontName('Times New Roman');
        $inputData->setDefaultFontSize(14);

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));

        $section->addText('Министерство образования и науки Астраханской области', array('lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('государственное автономное образовательное учреждение', array('lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('Астраханской области дополнительного образования', array('lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('«Региональный школьный технопарк»', array('bold' => true, 'lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('ГАОУ АО ДО «РШТ»', array('bold' => true, 'lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('ПРИКАЗ', array('bold' => true, 'lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addTextBreak(2);


        $text = ' ';
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $text . '.docx"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');

        return $inputData;
    }
    public static function generateOrderTrainingTransfer($orderId)
    {
        ini_set('memory_limit', '512M');

        $inputData = new PhpWord();
        $inputData->setDefaultFontName('Times New Roman');
        $inputData->setDefaultFontSize(14);

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));

        $section->addText('Министерство образования и науки Астраханской области', array('lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('государственное автономное образовательное учреждение', array('lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('Астраханской области дополнительного образования', array('lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('«Региональный школьный технопарк»', array('bold' => true, 'lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('ГАОУ АО ДО «РШТ»', array('bold' => true, 'lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('ПРИКАЗ', array('bold' => true, 'lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addTextBreak(2);


        $text = ' ';
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $text . '.docx"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');

        return $inputData;
    }
}