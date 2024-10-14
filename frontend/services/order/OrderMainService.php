<?php
namespace app\services\order;
use app\models\work\general\OrderPeopleWork;
use app\models\work\order\ExpireWork;
use app\models\work\order\OrderMainWork;
use common\helpers\files\filenames\OrderMainFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\models\scaffold\OrderMain;
use common\repositories\general\OrderPeopleRepository;
use common\repositories\order\OrderMainRepository;
use common\services\general\files\FileService;
use frontend\events\expire\ExpireCreateEvent;
use frontend\events\general\FileCreateEvent;
use frontend\events\general\OrderPeopleCreateEvent;
use frontend\models\work\document_in_out\DocumentInWork;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;

class OrderMainService {
    private FileService $fileService;
    private OrderPeopleRepository $orderPeopleRepository;
    private OrderMainFileNameGenerator $filenameGenerator;

    public function __construct(
        FileService $fileService,
        OrderMainFileNameGenerator $filenameGenerator,
        OrderPeopleRepository $orderPeopleRepository
    )
    {
        $this->orderPeopleRepository = $orderPeopleRepository;
        $this->fileService = $fileService;
        $this->filenameGenerator = $filenameGenerator;
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
    public function getResponsiblePeopleTable(int $modelId)
    {
        $responsiblePeople = $this->orderPeopleRepository->getResponsiblePeople($modelId);
        return HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Ответственные'], ArrayHelper::getColumn($responsiblePeople, 'fullFio'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-people'),
                    ['id' => ArrayHelper::getColumn($responsiblePeople, 'id'), 'modelId' => array_fill(0, count($responsiblePeople), $modelId)])
            ]
        );
    }
    public function getFilesInstances(OrderMainWork $model)
    {
        $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
        $model->docFiles = UploadedFile::getInstances($model, 'docFiles');
    }
    public function addExpireEvent($docs, $regulation, $model) {
        foreach ($docs as $doc) {
            if($doc != NULL) {
                $model->recordEvent(new ExpireCreateEvent($model->id,
                    NULL, $doc, 1, 1), ExpireWork::class);
            }
        }
        foreach ($regulation as $reg) {
            if($reg != NULL) {
                $model->recordEvent(new ExpireCreateEvent($model->id,
                    $reg, NULL, 1, 1), ExpireWork::class);
            }
        }
    }
    public function addOrderPeopleEvent($respPeople, $model)
    {
        if ($respPeople[0] != NULL) {
            for ($i = 0; $i < count($respPeople); $i++) {
                $model->recordEvent(new OrderPeopleCreateEvent($respPeople[$i], $model->id), OrderPeopleWork::class);
            }
        }
    }
    public function saveFilesFromModel(OrderMainWork $model)
    {
        if ($model->scanFile !== null) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_SCAN);
            $this->fileService->uploadFile(
                $model->scanFile,
                $filename,
                [
                    'tableName' => OrderMainWork::tableName(),
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
                        'tableName' => OrderMainWork::tableName(),
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
}