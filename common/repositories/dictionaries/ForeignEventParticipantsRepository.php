<?php

namespace common\repositories\dictionaries;

use DomainException;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\dictionaries\PersonalDataParticipantWork;
use Yii;

class ForeignEventParticipantsRepository
{
    public function get($id)
    {
        return ForeignEventParticipantsWork::find()->where(['id' => $id])->one();
    }

    public function delete(ForeignEventParticipantsWork $participant)
    {
        if (!$participant->delete()) {
            throw new DomainException('Ошибка удаления участника. Проблемы: '.json_encode($participant->getErrors()));
        }

        return $participant->id;
    }

    public function save(ForeignEventParticipantsWork $participant)
    {
        if (!$participant->save()) {
            throw new DomainException('Ошибка сохранения участника. Проблемы: '.json_encode($participant->getErrors()));
        }

        return $participant->id;
    }
}