<?php

namespace common\repositories\general;

use common\components\traits\CommonDatabaseFunctions;
use common\repositories\dictionaries\PeopleRepository;
use DomainException;
use frontend\models\work\general\PeopleStampWork;
use frontend\models\work\general\PeopleWork;

class PeopleStampRepository
{
    use CommonDatabaseFunctions;

    private PeopleRepository $peopleRepository;

    public function __construct(PeopleRepository $peopleRepository)
    {
        $this->peopleRepository = $peopleRepository;
    }

    public function get($id)
    {
        return PeopleStampWork::find()->where(['id' => $id])->one();
    }

    // Поиск такого же отпечатка по данным (чтобы не создавать новый)
    public function getSimilar(PeopleWork $people)
    {
        return PeopleStampWork::find()
            ->where(['people_id' => $people->id])
            ->andWhere(['surname' => $people->surname])
            ->andWhere(['genitive_surname' => $people->genitive_surname])
            ->andWhere(['position_id' => $people->position_id])
            ->andWhere(['company_id' => $people->company_id])
            ->one();
    }

    public function save(PeopleStampWork $stamp)
    {
        if (!$stamp->save()) {
            throw new DomainException('Ошибка сохранения человека. Проблемы: '.json_encode($stamp->getErrors()));
        }

        return $stamp->id;
    }
}