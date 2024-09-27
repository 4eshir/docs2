<?php

namespace common\repositories\educational;

use common\models\scaffold\AuthorProgram;
use DomainException;
use frontend\models\work\educational\AuthorProgramWork;
use frontend\models\work\educational\BranchProgramWork;
use frontend\models\work\educational\ThematicPlanWork;
use frontend\models\work\educational\TrainingProgramWork;
use Yii;

class TrainingProgramRepository
{
    public function get($id)
    {
        return TrainingProgramWork::find()->where(['id' => $id])->one();
    }

    public function getBranches($id)
    {
        return BranchProgramWork::find()->where(['training_program_id' => $id])->orderBy(['branch' => SORT_ASC])->all();
    }

    public function prepareResetBranches($eventId)
    {
        $branches = $this->getBranches($eventId);
        $commands = [];
        foreach ($branches as $branch) {
            $command = Yii::$app->db->createCommand();
            $command->delete(BranchProgramWork::tableName(), ['id' => $branch->id]);
            $commands[] = $command->getRawSql();
        }

        return $commands;
    }

    public function prepareConnectBranches($eventId, $branches)
    {
        $commands = [];
        foreach ($branches as $branch) {
            $model = BranchProgramWork::fill($eventId, $branch);
            $command = Yii::$app->db->createCommand();
            $command->insert($model::tableName(), $model->getAttributes());
            $commands[] = $command->getRawSql();
        }

        return $commands;
    }

    public function save(TrainingProgramWork $program)
    {
        if (!$program->save()) {
            throw new DomainException('Ошибка сохранения образовательной программы. Проблемы: '.json_encode($program->getErrors()));
        }
        return $program->id;
    }

    public function getThematicPlan($programId)
    {
        return ThematicPlanWork::find()->where(['training_program_id' => $programId])->all();
    }

    public function getAuthors($programId)
    {
        return AuthorProgramWork::find()->where(['training_program_id' => $programId])->all();
    }

    public function delete(TrainingProgramWork $model)
    {
    }
}