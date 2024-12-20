<?php

namespace common\repositories\dictionaries;

use DomainException;
use frontend\events\foreign_event_participants\PersonalDataParticipantDetachEvent;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\dictionaries\PersonalDataParticipantWork;
use frontend\models\work\general\RussianNamesWork;
use InvalidArgumentException;
use Yii;

class ForeignEventParticipantsRepository
{
    const SORT_ID = 0;
    const SORT_FIO = 1;

    public function get($id)
    {
        return ForeignEventParticipantsWork::find()->where(['id' => $id])->one();
    }

    public function getParticipants(array $ids)
    {
        return ForeignEventParticipantsWork::find()->where(['IN', 'id', $ids])->all();
    }

    public function getSortedList($sort = self::SORT_ID)
    {
        $participants = ForeignEventParticipantsWork::find();

        switch ($sort) {
            case self::SORT_ID:
                $participants = $participants->orderBy(['id' => SORT_ASC]);
                break;
            case self::SORT_FIO:
                $participants = $participants->orderBy(['surname' => SORT_ASC, 'firstname' => SORT_ASC, 'patronymic' => SORT_ASC]);
                break;
            default:
                throw new InvalidArgumentException('Неизвестный тип сортировки');
        }

        return $participants->all();
    }

    public function prepareUpdate(ForeignEventParticipantsWork $model)
    {
        $attributes = $model->getAttributes();
        unset($attributes['id']);

        $command = Yii::$app->db->createCommand();
        $command->update($model::tableName(), $attributes, ['id' => $model->id]);
        return $command->getRawSql();
    }

    public function getSexByName(string $name)
    {
        $searchName = RussianNamesWork::find()->where(['name' => $name])->one();
        if ($searchName == null) {
            return 2;
        }

        if ($searchName->Sex == "М") {
            return 0;
        }

        return 1;
    }

    public function delete(ForeignEventParticipantsWork $participant)
    {
        $participant->recordEvent(new PersonalDataParticipantDetachEvent($participant->id), PersonalDataParticipantWork::class);
        $participant->releaseEvents();

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