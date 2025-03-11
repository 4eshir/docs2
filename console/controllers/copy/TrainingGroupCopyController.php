<?php

namespace console\controllers\copy;

use common\services\general\PeopleStampService;
use console\helper\FileTransferHelper;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use Yii;
use yii\console\Controller;

class TrainingGroupCopyController extends Controller
{
    private FileTransferHelper $fileTransferHelper;
    private PeopleStampService $peopleStampService;
    public function __construct(
        $id,
        $module,
        FileTransferHelper $fileTransferHelper,
        PeopleStampService $peopleStampService,
        $config = [])
    {
        $this->fileTransferHelper = $fileTransferHelper;
        $this->peopleStampService = $peopleStampService;
        parent::__construct($id, $module, $config);
    }
    public function CopyTrainingGroup()
    {
        $query = Yii::$app->old_db->createCommand("SELECT * FROM training_group");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('training_group',
                [
                    'id' => $record['id'],
                    'number' => $record['number'],
                    'training_program_id' => $record['training_program_id'],
                    'teacher_id' => $record['teacher_id'] != '' ? $this->peopleStampService->createStampFromPeople($record['teacher_id']) : NULL,
                    'start_date' => $record['start_date'],
                    'finish_date' => $record['finish_date'],
                    'open' => $record['open'],
                    'budget' => $record['budget'],
                    'branch' => $record['branch_id'],
                    'order_stop' => $record['order_stop'],
                    'archive' => $record['archive'],
                    'protection_date' => $record['protection_date'],
                    'protection_confirm' => $record['protection_confirm'],
                    'is_network' => $record['is_network'],
                    'state' => 0, //???
                    'creator_id' => $record['creator_id'],
                    'last_edit_id' => $record['last_edit_id'],
                ]
            );
            //файлы
            /*
            $this->fileTransferHelper->createFiles(
                [
                    'scan' => ,
                    'doc' => ,
                    'app' => ,
                    'table' => TrainingGroupWork::tableName(),
                    'row' => $record['id'],
                ]
            );
            */
            $command->execute();
        }
    }
    public function CopyTeacherGroup(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM teacher_group");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('teacher_group',
            [
                'id' => $record['id'],
                'teacher_id' => $record['teacher_id'] != '' ? $this->peopleStampService->createStampFromPeople($record['teacher_id']) : NULL,
                'training_group_id' => $record['training_group_id'],
            ]
            );
            $command->execute();
        }
    }
    public function CopyTrainingGroupExpert(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM training_group_expert");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('training_group_expert',
                [
                    'id' => $record['id'],
                    'expert_id' => $record['expert_id'] != '' ? $this->peopleStampService->createStampFromPeople($record['expert_id']) : NULL,
                    'training_group_id' => $record['training_group_id'],
                    'expert_type' => $record['expert_type_id'],
                ]
            );
            $command->execute();
        }
    }
    public function CopyTrainingGroupLesson(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM training_group_lesson");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('training_group_lesson',
                [
                    'id' => $record['id'],
                    'lesson_date' => $record['lesson_date'],
                    'lesson_start_time' => $record['lesson_start_time'],
                    'lesson_end_time' => $record['lesson_end_time'],
                    'duration' => $record['duration'],
                    'branch' => $record['branch_id'],
                    'auditorium_id' => $record['auditorium_id'],
                    'training_group_id' => $record['training_group_id'],
                ]
            );
            $command->execute();
        }

    }
    public function CopyGroupProjectThemes(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM group_project_themes");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('group_project_themes',
                [
                    'id' => $record['id'],
                    'training_group_id' => $record['training_group_id'],
                    'project_theme_id' => $record['project_theme_id'],
                    'confirm' => $record['confirm'],
                ]
            );
            $command->execute();
        }
    }
    public function CopyTrainingGroupParticipant(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM training_group_participant");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('training_group_participant',
                [
                    'id' => $record['id'],
                    'participant_id' => $record['participant_id'],
                    'certificat_number' => $record['certificate_number'],
                    'send_method' => $record['send_method_id'],
                    'training_group_id' => $record['training_group_id'],
                    'status' => $record['status'], //???
                    'success' => $record['success'], //???
                    'points' => $record['points'],
                    'group_project_themes_id' => $record['group_project_themes_id'],
                ]
            );
            $command->execute();
        }
    }
    public function actionDeleteTrainingGroupParticipant()
    {
        Yii::$app->db->createCommand()->delete('training_group_participant')->execute();
    }
    public function actionDeleteGroupProjectThemes(){
        Yii::$app->db->createCommand()->delete('group_project_themes')->execute();
    }
    public function actionDeleteTrainingGroupLesson(){
        Yii::$app->db->createCommand()->delete('training_group_lesson')->execute();
    }
    public function actionDeleteTrainingGroupExpert(){
        Yii::$app->db->createCommand()->delete('training_group_expert')->execute();
    }
    public function actionDeleteTeacherGroup(){
        Yii::$app->db->createCommand()->delete('teacher_group')->execute();
    }
    public function actionDeleteTrainingGroup(){
        Yii::$app->db->createCommand()->delete('training_group')->execute();
    }
    public function actionDeleteAll()
    {
        $this->actionDeleteTrainingGroupParticipant();
        $this->actionDeleteGroupProjectThemes();
        $this->actionDeleteTrainingGroupLesson();
        $this->actionDeleteTrainingGroupExpert();
        $this->actionDeleteTeacherGroup();
        $this->actionDeleteTrainingGroup();
    }
    public function actionCopyAll(){
        $this->CopyTrainingGroup();
        $this->CopyTeacherGroup();
        $this->CopyTrainingGroupExpert();
        $this->CopyTrainingGroupLesson();
        $this->CopyGroupProjectThemes();
        $this->CopyTrainingGroupParticipant();
    }
}