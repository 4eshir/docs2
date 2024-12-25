<?php

namespace frontend\models\work\educational\training_group;

use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\models\scaffold\TrainingGroup;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\educational\TeacherGroupRepository;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use frontend\models\work\general\PeopleStampWork;
use frontend\models\work\general\PeopleWork;
use InvalidArgumentException;
use Yii;

/**
 * @property TrainingProgramWork $trainingProgramWork
 * @property PeopleStampWork $teacherWork
 */

class TrainingGroupWork extends TrainingGroup
{
    use EventTrait;

    public static function fill(
        $startDate,
        $endDate,
        $open,
        $budget,
        $branch,
        $orderStop,
        $archive,
        $protectionDate,
        $protectionConfirm,
        $isNetwork,
        $state,
        $creatorId,
        $lastEditId
    )
    {
        $entity = new static();
        $entity->start_date = $startDate;
        $entity->finish_date = $endDate;
        $entity->open = $open;
        $entity->budget = $budget;
        $entity->branch = $branch;
        $entity->order_stop = $orderStop;
        $entity->archive = $archive;
        $entity->protection_date = $protectionDate;
        $entity->protection_confirm = $protectionConfirm;
        $entity->is_network = $isNetwork;
        $entity->state = $state;
        $entity->creator_id = $creatorId;
        $entity->last_edit_id = $lastEditId;

        return $entity;
    }

    public function generateNumber($teacherId)
    {
        $level = $this->trainingProgramWork->level;
        $level++;
        $thematicDirection = $this->trainingProgramWork->thematic_direction ? Yii::$app->thematicDirection->getAbbreviation($this->trainingProgramWork->thematic_direction) : '';
        $date = DateFormatter::format($this->start_date, DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator);
        $teacherCode = (Yii::createObject(PeopleRepository::class)->get($teacherId))->short;
        $addCode = 1;

        $sameNameGroups = TrainingGroupWork::find()->where(['like', 'number', $this->number.'%', false])->andWhere(['!=', 'id', $this->id])->all();
        $pattern = '/\.(d+)$/';
        for ($i = 0; $i < count($sameNameGroups) - 1; $i++) {
            preg_match($pattern, $sameNameGroups[$i]->number, $matches);
            $number1 = $matches[1];
            preg_match($pattern, $sameNameGroups[$i + 1]->number, $matches);
            $number2 = $matches[1];
            if ($number2 - $number1 > 1) {
                $addCode = (string)((int)$number1 + 1);
                break;
            }
            $addCode = (string)((int)$number2 + 1);
        }

        $this->number = "$thematicDirection.$level.$teacherCode.$date.$addCode";

        return $this->number;
    }

    /**
     * Возвращает массив
     * link => форматированная ссылка на документ
     * id => ID записи в таблице files
     * @param $filetype
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getFileLinks($filetype)
    {
        if (!array_key_exists($filetype, FilesHelper::getFileTypes())) {
            throw new InvalidArgumentException('Неизвестный тип файла');
        }

        $addPath = '';
        switch ($filetype) {
            case FilesHelper::TYPE_PHOTO:
                $addPath = FilesHelper::createAdditionalPath(TrainingGroupWork::tableName(), FilesHelper::TYPE_PHOTO);
                break;
            case FilesHelper::TYPE_PRESENTATION:
                $addPath = FilesHelper::createAdditionalPath(TrainingGroupWork::tableName(), FilesHelper::TYPE_PRESENTATION);
                break;
            case FilesHelper::TYPE_WORK:
                $addPath = FilesHelper::createAdditionalPath(TrainingGroupWork::tableName(), FilesHelper::TYPE_WORK);
                break;
        }

        return FilesHelper::createFileLinks($this, $filetype, $addPath);
    }

    public function getTrainingProgramWork()
    {
        return $this->hasOne(TrainingProgramWork::class, ['id' => 'training_program_id']);
    }

    public function getTeacherWork()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'teacher_id']);
    }
}