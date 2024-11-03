<?php

namespace frontend\services\event;

use app\models\work\event\ForeignEventWork;
use app\models\work\team\ActParticipantWork;
use common\helpers\files\filenames\ForeignEventFileNameGenerator;
use common\helpers\files\filenames\OrderMainFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\repositories\act_participant\ActParticipantRepository;
use common\services\general\files\FileService;
use frontend\events\general\FileCreateEvent;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class ForeignEventService
{
    private FileService $fileService;
    private ForeignEventFileNameGenerator $filenameGenerator;
    private ActParticipantRepository $actParticipantRepository;
    public function __construct(
        FileService $fileService,
        ForeignEventFileNameGenerator $filenameGenerator,
        ActParticipantRepository $actParticipantRepository
    )
    {
        $this->fileService = $fileService;
        $this->filenameGenerator = $filenameGenerator;
        $this->actParticipantRepository = $actParticipantRepository;
    }
    public function saveFilesFromModel(ForeignEventWork $model , $actFiles , $number)
    {
        if ($actFiles != NULL) {
            for ($i = 1; $i < count($actFiles) + 1; $i++) {
                $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_DOC, ['counter' => $i, 'number' => $number]);
                $this->fileService->uploadFile(
                    $actFiles[$i - 1],
                    $filename,
                    [
                        'tableName' => ForeignEventWork::tableName(),
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
    public function getForeignEventTable(ForeignEventWork $model)
    {
        /* @var ActParticipantWork $actParticipan t*/
        $actParticipant = $this->actParticipantRepository->getByForeignEventId($model->id);
        $foreignEvent = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['АКТЫ'], ArrayHelper::getColumn($actParticipant, 'id'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-foreign-event'),
                    [
                        'modelId' => array_fill(0, count($actParticipant), $actParticipant->id),
                        'fileId' => ArrayHelper::getColumn($actParticipant, 'id')])
            ]
        );
        return $foreignEvent;
    }
}