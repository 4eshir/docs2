<?php

namespace app\services\act_participant;

use app\events\act_participant\ActParticipantCreateEvent;
use app\models\work\team\ActParticipantWork;
use app\models\work\team\TeamNameWork;
use common\helpers\files\filenames\ActParticipantFileNameGenerator;
use common\helpers\files\filenames\OrderMainFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\models\scaffold\ActParticipant;
use common\repositories\team\TeamRepository;
use common\services\general\files\FileService;
use frontend\events\general\FileCreateEvent;
use frontend\forms\OrderEventForm;

class ActParticipantService
{
    public TeamRepository $teamRepository;
    private ActParticipantFileNameGenerator $filenameGenerator;
    private FileService $fileService;
    public function __construct(
        TeamRepository $teamRepository,
        ActParticipantFileNameGenerator $filenameGenerator,
        FileService $fileService
    )
    {
        $this->teamRepository = $teamRepository;
        $this->filenameGenerator = $filenameGenerator;
        $this->fileService = $fileService;
    }
    public function saveFilesFromModel(ActParticipantWork $model , $actFiles)
    {
        if ($actFiles != NULL) {
            for ($i = 1; $i < count($actFiles) + 1; $i++) {
                $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_DOC, ['counter' => $i, 'extensions' => $actFiles[$i - 1]]);
                $this->fileService->uploadFile(
                    $actFiles[$i - 1],
                    $filename,
                    [
                        'tableName' => ActParticipantWork::tableName(),
                        'fileType' => FilesHelper::TYPE_DOC
                    ]
                );
                $model->recordEvent(
                    new FileCreateEvent(
                        $model::tableName(),
                        $model->id,
                        FilesHelper::TYPE_DOC,
                        $filename,
                        FilesHelper::LOAD_TYPE_SINGLE
                    ),
                    get_class($model)
                );
            }
        }

    }
    public function addActParticipantEvent(OrderEventForm $model, $teams, $persons, $foreignEventId)
    {
        foreach($teams as $team) {
            /* @var TeamNameWork $teamRecord */
            $teamRecord = $this->teamRepository->getByNameAndForeignEventId($foreignEventId , $team['team'][0]);
            $model->recordEvent(new ActParticipantCreateEvent(
                $team['teachers'][0][0],
                $team['teachers2'][0][0],
                $teamRecord->id, //team_name
                $foreignEventId,
                $team['branches'][0][0],
                $team['focus'][0][0],
                1, //   1 - team ,  0 - person
                NULL, //allow remote
                $team['nominations'][0][0],
                $team['formRealization'][0][0]
            ),
                ActParticipantWork::class
            );
        }
        foreach($persons as $person) {
            $model->recordEvent(new ActParticipantCreateEvent(
                $person['teachers'][0][0],
                $person['teachers2'][0][0],
                NULL, //team_name
                $foreignEventId,
                $person['branches'][0][0],
                $person['focus'][0][0],
                0, //1 - team ,  0 - person
                NULL, //allow remote
                $person['nominations'][0],
                $person['formRealization'][0][0]
            ),
                ActParticipantWork::class
            );
        }
    }
}