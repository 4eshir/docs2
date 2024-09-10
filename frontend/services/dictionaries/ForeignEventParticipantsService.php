<?php

namespace frontend\services\dictionaries;

use common\models\scaffold\ForeignEventParticipants;
use common\models\scaffold\PeoplePositionCompanyBranch;
use common\models\scaffold\PeopleStamp;
use common\models\scaffold\PersonalDataParticipant;
use common\models\scaffold\Position;
use common\repositories\dictionaries\PersonalDataParticipantRepository;
use common\repositories\general\PeoplePositionCompanyBranchRepository;
use common\repositories\general\PeopleStampRepository;

class ForeignEventParticipantsService
{
    private PersonalDataParticipantRepository $personalDataRepository;

    public function __construct(
        PersonalDataParticipantRepository $personalDataRepository
    )
    {
        $this->personalDataRepository = $personalDataRepository;
    }

    /**
     * Возвращает список ошибок, если список пуст - проблем нет
     * @param $entityId
     * @return array
     */
    public function isAvailableDelete($entityId)
    {
        $personalData = $this->personalDataRepository->checkDeleteAvailable(PersonalDataParticipant::tableName(), ForeignEventParticipants::tableName(), $entityId);

        return $personalData;
    }
}