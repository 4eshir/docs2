<?php

namespace console\controllers\copy;

use common\repositories\act_participant\ActParticipantRepository;
use common\services\general\PeopleStampService;
use frontend\models\work\team\ActParticipantWork;
use Yii;
use yii\console\Application;
use yii\console\Controller;

class ActCopyController extends Controller
{
    private ActParticipantRepository $actParticipantRepository;
    private PeopleStampService $peopleStampService;
    public function __construct(
        $id,
        $module,
        ActParticipantRepository $actParticipantRepository,
        PeopleStampService $peopleStampService,
        $config = [])
    {
        $this->actParticipantRepository = $actParticipantRepository;
        $this->peopleStampService = $peopleStampService;
        parent::__construct($id, $module, $config);
    }

    public function actionTeamNameCopy(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM team_name");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('team_name', $record);
            $command->execute();
        }
    }
    public function actionActCopy()
    {
        $query = Yii::$app->old_db->createCommand("SELECT * FROM team_name");
        foreach ($query->queryAll() as $record) {
            $teamNameId  = $record['id'];
            $acts = Yii::$app->old_db->createCommand("SELECT * FROM team WHERE team_name_id = $teamNameId")->queryAll();
            if (count($acts) > 0) {
                $act = $acts[0];
                $teacherParticipantId = $act['teacher_participant_id'];
                $team = is_null($act['participant_id']) ? NULL : $teamNameId;
                $teacherParticipants = Yii::$app->old_db->createCommand("SELECT * FROM teacher_participant WHERE id = $teacherParticipantId")->queryAll();
                //act_participant
                $actModel = ActParticipantWork::fill(
                    $teacherParticipants[0]['teacher_id'] != '' ? $this->peopleStampService->createStampFromPeople($teacherParticipants[0]['teacher_id']) : NULL,
                    $teacherParticipants[0]['teacher2_id'] != '' ? $this->peopleStampService->createStampFromPeople($teacherParticipants[0]['teacher2_id']) : NULL,
                    $team,
                    $record['foreign_event_id'],
                    $teacherParticipants[0]['focus'],
                    NULL,
                    $teacherParticipants[0]['allow_remote_id'],
                    $teacherParticipants[0]['nomination'],
                    $teacherParticipants[0]['allow_remote_id']
                );
                $this->actParticipantRepository->saveph($actModel);
                //act_participant_branch
                $branches = Yii::$app->old_db->createCommand("SELECT * FROM teacher_participant_branch WHERE teacher_participant_id = $teacherParticipantId")->queryAll();
                foreach ($branches as $branch) {
                    $command = Yii::$app->db->createCommand();
                    $command->insert('act_participant_branch',
                        [
                            'act_participant_id' => $actModel->id,
                            'branch' => $branch,
                        ]
                    );
                    $command->execute();
                }
                //squad_participant
                foreach ($acts as $act) {
                    $command = Yii::$app->db->createCommand();
                    $partId = $act['teacher_participant_id'];
                    $part = Yii::$app->old_db->createCommand("SELECT * FROM teacher_participant WHERE id = $partId")->queryAll();
                    if (is_null($team)) {
                        $command->insert('squad_participant',
                            [
                                'id' => $act['id'],
                                'act_participant_id' => $actModel->id,
                                'participant_id' => $part[0]['participant_id'],
                            ]
                        );
                    } else {
                        $command->insert('squad_participant', [
                            'id' => $act['id'],
                            'act_participant_id' => $actModel->id,
                            'participant_id' => $part[0]['participant_id'],
                        ]);
                    }
                    $command->execute();
                }
            }
        }
    }
    public function actionCopyAll(){
        $this->actionTeamNameCopy();
        $this->actionActCopy();
    }
    public function actionDeleteTeamName()
    {
        Yii::$app->db->createCommand()->delete('team_name')->execute();
    }
    public function actionDeleteActParticipant()
    {
        Yii::$app->db->createCommand()->delete('act_participant')->execute();
    }
    public function actionDeleteSquadParticipant()
    {
        Yii::$app->db->createCommand()->delete('squad_participant')->execute();
    }
    public function actionDeleteActParticipantBranch()
    {
        Yii::$app->db->createCommand()->delete('act_participant_branch')->execute();
    }
    public function actionDeleteAll()
    {
        $this->actionDeleteActParticipantBranch();
        $this->actionDeleteSquadParticipant();
        $this->actionDeleteActParticipant();
        $this->actionDeleteTeamName();
    }
}