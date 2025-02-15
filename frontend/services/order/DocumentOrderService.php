<?php

namespace frontend\services\order;

use common\repositories\general\PeopleStampRepository;
use common\services\general\PeopleStampService;
use frontend\models\work\general\OrderPeopleWork;
use frontend\models\work\order\DocumentOrderWork;
use common\helpers\files\filenames\DocumentOrderFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\services\general\files\FileService;
use frontend\events\general\FileCreateEvent;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;

class DocumentOrderService
{

    private FileService $fileService;
    private DocumentOrderFileNameGenerator $filenameGenerator;
    private PeopleStampService $peopleStampService;
    private PeopleStampRepository $peopleStampRepository;

    public function __construct(
        FileService $fileService,
        DocumentOrderFileNameGenerator $filenameGenerator,
        PeopleStampService $peopleStampService,
        PeopleStampRepository $peopleStampRepository
    )
    {
        $this->fileService = $fileService;
        $this->filenameGenerator = $filenameGenerator;
        $this->peopleStampService = $peopleStampService;
        $this->peopleStampRepository = $peopleStampRepository;
    }
    public function createOrderPeopleArray(array $data)
    {
        $result = [];
        foreach ($data as $item) {
            /** @var OrderPeopleWork $item */
            $result[] = $item->getFullFio();
        }
        return $result;
    }
    public function getFilesInstances($model)
    {
        $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
        $model->docFiles = UploadedFile::getInstances($model, 'docFiles');
        $model->appFiles = UploadedFile::getInstances($model, 'appFiles');
    }
    public function saveFilesFromModel($model)
    {
        if ($model->scanFile !== null) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_SCAN);
            $this->fileService->uploadFile(
                $model->scanFile,
                $filename,
                [
                    'tableName' => DocumentOrderWork::tableName(),
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
        if ($model->docFiles != NULL) {
            for ($i = 1; $i < count($model->docFiles) + 1; $i++) {
                $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_DOC, ['counter' => $i]);

                $this->fileService->uploadFile(
                    $model->docFiles[$i - 1],
                    $filename,
                    [
                        'tableName' => DocumentOrderWork::tableName(),
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
    public function getUploadedFilesTables($model)
    {
        $scanLinks = $model->getFileLinks(FilesHelper::TYPE_SCAN);
        $scanFile = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($scanLinks, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($scanLinks), $model->id), 'fileId' => ArrayHelper::getColumn($scanLinks, 'id')])
            ]
        );

        $docLinks = $model->getFileLinks(FilesHelper::TYPE_DOC);
        $docFiles = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($docLinks, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($docLinks), $model->id), 'fileId' => ArrayHelper::getColumn($docLinks, 'id')])
            ]
        );

        $appLinks = $model->getFileLinks(FilesHelper::TYPE_APP);
        $appFiles = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($appLinks, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($appLinks), $model->id), 'fileId' => ArrayHelper::getColumn($appLinks, 'id')])
            ]
        );

        return ['scan' => $scanFile, 'docs' => $docFiles, 'app' => $appFiles];
    }
    public function getPeopleStamps($model)
    {
        if ($model->executor_id != "") {
            $peopleStampId = $this->peopleStampService->createStampFromPeople($model->executor_id);
            $model->executor_id = $peopleStampId;
        }
        if ($model->signed_id != "") {
            $peopleStampId = $this->peopleStampService->createStampFromPeople($model->signed_id);
            $model->signed_id = $peopleStampId;
        }
        if ($model->bring_id != "") {
            $peopleStampId = $this->peopleStampService->createStampFromPeople($model->bring_id);
            $model->bring_id = $peopleStampId;
        }
    }
    public function setResponsiblePeople($responsiblePeople, $model)
    {
        foreach ($responsiblePeople as $index => $person) {
            $person = $this->peopleStampRepository->get($person);
            $responsiblePeople[$index] = $person->people_id;
        }
        $model->responsible_id = $responsiblePeople;
    }
}