<?php

namespace common\services\general;

use common\repositories\dictionaries\PeopleRepository;
use common\repositories\general\PeopleStampRepository;
use frontend\models\work\general\PeopleStampWork;
use frontend\models\work\general\PeopleWork;

class PeopleStampService
{
    private PeopleRepository $peopleRepository;
    private PeopleStampRepository $stampRepository;

    public function __construct(PeopleRepository $peopleRepository, PeopleStampRepository $stampRepository)
    {
        $this->peopleRepository = $peopleRepository;
        $this->stampRepository = $stampRepository;
    }

    /**
     * Создает копию человека по его id
     * Возвращает id копии
     * @param $peopleId
     * @return int
     */
    public function createStampFromPeople($peopleId)
    {
        /** @var PeopleWork $people */
        $people = $this->peopleRepository->get($peopleId);
        $stamp = PeopleStampWork::fill($people->id, $people->surname, $people->genitive_surname, $people->position_id, $people->company_id);

        return $this->stampRepository->save($stamp);
    }

}