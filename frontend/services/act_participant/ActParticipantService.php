<?php
namespace app\services\act_participant;
use app\events\act_participant\ActParticipantCreateEvent;
use app\models\work\team\ActParticipantWork;
use app\models\work\team\TeamWork;
use app\services\team\TeamService;
use common\helpers\files\filenames\ActParticipantFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\models\scaffold\ActParticipant;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\team\TeamRepository;
use common\services\general\files\FileService;
use frontend\events\general\FileCreateEvent;
use frontend\models\forms\ActParticipantForm;
use PHPUnit\Util\Xml\ValidationResult;
use yii\helpers\Html;
use yii\web\UploadedFile;
class ActParticipantService
{
    public TeamRepository $teamRepository;
    public TeamService $teamService;
    private ActParticipantFileNameGenerator $filenameGenerator;
    private ActParticipantRepository $actParticipantRepository;
    private FileService $fileService;
    public function __construct(
        TeamRepository $teamRepository,
        TeamService $teamService,
        ActParticipantFileNameGenerator $filenameGenerator,
        ActParticipantRepository $actParticipantRepository,
        FileService $fileService
    )
    {
        $this->teamRepository = $teamRepository;
        $this->teamService = $teamService;
        $this->filenameGenerator = $filenameGenerator;
        $this->actParticipantRepository = $actParticipantRepository;
        $this->fileService = $fileService;
    }
    public function getFilesInstance(ActParticipantForm $modelActParticipant, $index)
    {
        $modelActParticipant->actFiles = UploadedFile::getInstance($modelActParticipant,  "[{$index}]actFiles");
    }
    public function saveFilesFromModel(ActParticipantWork $model, $index)
    {
        if ($model->actFiles != null) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_SCAN, ['counter' => $index]);
            $this->fileService->uploadFile(
                $model->actFiles,
                $filename,
                [
                    'tableName' => ActParticipantWork::tableName(),
                    'fileType' => FilesHelper::TYPE_SCAN
                ]
            );
            $model->recordEvent(
                new FileCreateEvent(
                    $model::tableName(),
                    $model->id,
                    FilesHelper::TYPE_SCAN,
                    $filename,
                    FilesHelper::LOAD_TYPE_SINGLE
                ),
                get_class($model)
            );
        }
    }
    public function addActParticipantEvent($acts, $foreignEventId){
        $index = 0;
        foreach ($acts as $act){
           $modelActParticipantForm = ActParticipantForm::fill(
               $act["firstTeacher"],
               $act["secondTeacher"],
               $act["branch"],
               $act["focus"],
               $act["type"],
               NULL,
               $act["nomination"],
               $act["form"],
               $act["team"]
           );
           $modelActParticipantForm->foreignEventId = $foreignEventId;
           $this->getFilesInstance($modelActParticipantForm, $index);
           $teamNameId = $this->teamService->teamNameCreateEvent($foreignEventId, $act["team"]);
           $modelAct = ActParticipantWork::fill(
               $modelActParticipantForm->firstTeacher,
               $modelActParticipantForm->secondTeacher,
               $teamNameId,
               $foreignEventId,
               $modelActParticipantForm->branch,
               $modelActParticipantForm->focus,
               $modelActParticipantForm->type,
               $modelActParticipantForm->allowRemote,
               $modelActParticipantForm->nomination,
               $modelActParticipantForm->form,
           );
           $modelAct->actFiles = $modelActParticipantForm->actFiles;
           $modelAct->recordEvent(new ActParticipantCreateEvent($modelAct, $teamNameId, $foreignEventId), ActParticipantWork::class);
           $modelAct->releaseEvents();
           $this->saveFilesFromModel($modelAct, $index);
           $modelAct->releaseEvents();
           $index++;
       }
    }
}