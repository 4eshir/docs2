<?php
namespace app\services\order;
use app\models\work\general\OrderPeopleWork;
use app\models\work\order\ExpireWork;
use app\models\work\order\OrderMainWork;
use common\helpers\files\filenames\OrderMainFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\models\scaffold\OrderMain;
use common\repositories\expire\ExpireRepository;
use common\repositories\general\OrderPeopleRepository;
use common\repositories\order\OrderMainRepository;
use common\repositories\regulation\RegulationRepository;
use common\services\general\files\FileService;
use frontend\events\expire\ExpireCreateEvent;
use frontend\events\general\FileCreateEvent;
use frontend\events\general\OrderPeopleCreateEvent;
use frontend\models\work\document_in_out\DocumentInWork;
use frontend\models\work\regulation\RegulationWork;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;

class OrderMainService {
    private FileService $fileService;
    private OrderPeopleRepository $orderPeopleRepository;
    private RegulationRepository $regulationRepository;
    private ExpireRepository $expireRepository;
    private OrderMainRepository $orderMainRepository;
    private OrderMainFileNameGenerator $filenameGenerator;

    public function __construct(
        FileService $fileService,
        OrderMainFileNameGenerator $filenameGenerator,
        OrderMainRepository $orderMainRepository,
        OrderPeopleRepository $orderPeopleRepository,
        ExpireRepository $expireRepository,
        RegulationRepository $regulationRepository
    )
    {
        $this->orderPeopleRepository = $orderPeopleRepository;
        $this->fileService = $fileService;
        $this->expireRepository = $expireRepository;
        $this->regulationRepository = $regulationRepository;
        $this->orderMainRepository = $orderMainRepository;
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
    public function getUploadedFilesTables(OrderMainWork $model)
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
    public function createChangedDocumentsArray(array $data)
    {
        $result = [];
        foreach ($data as $item) {
            /** @var ExpireWork $item */

            if ($item->expire_order_id != NULL) {
                /** @var OrderMainWork $model */
                $model = $this->orderMainRepository->get($item->expire_order_id);
                $result[] = $model->order_name.'  ('.$item->getStatus().')';
            }
            if ($item->expire_regulation_id != NULL) {
                /** @var RegulationWork $model */
                $model = $this->regulationRepository->get($item->expire_regulation_id);
                $result[] =  $model->name.'  ('.$item->getStatus().')';
            }

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
    public function getChangedDocumentsTable(int $modelId)
    {
        $expires = $this->expireRepository->getExpireByActiveRegulationId($modelId);

        return HtmlBuilder::createTableWithActionButtons(
                    [
                        array_merge(['Тип документа'], ArrayHelper::getColumn($expires, 'type')),
                        array_merge(['Номер документа'], ArrayHelper::getColumn($expires, 'number')),
                        array_merge(['Статус'], ArrayHelper::getColumn($expires, 'status'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-document'),
                    ['id' => ArrayHelper::getColumn($expires, 'id'), 'modelId' => array_fill(0, count($expires), $modelId)])
            ]
        );
    }
    public function getFilesInstances(OrderMainWork $model)
    {
        $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
        $model->docFiles = UploadedFile::getInstances($model, 'docFiles');
    }
    public function addExpireEvent($docs, $regulation, $status, $model) {
        $old_docs = $docs;
        $old_regulation = $regulation;
        $docs = array_unique($docs);
        $regulation = array_unique($regulation);
        foreach ($docs as $doc) {
            $index = $this->getIndexByElement($old_docs, $doc);
            if ($doc != NULL && $status[$index] != NULL && $doc != $model->id) {
                if ($this->expireRepository->checkUnique($model->id, NULL, $doc, 1, 1) &&
                    $this->expireRepository->checkUnique($model->id, NULL, $doc, 1, 2)) {
                    $model->recordEvent(new ExpireCreateEvent($model->id,
                        NULL, $doc, 1, $status[$index]), ExpireWork::class);
                }
            }
        }
        foreach ($regulation as $reg) {
            $index = $this->getIndexByElement($old_regulation, $reg);
            if ($reg != NULL && $status[$index] != NULL) {
                if ($this->expireRepository->checkUnique($model->id, $reg, NULL, 1, 1) &&
                    $this->expireRepository->checkUnique($model->id, $reg, NULL, 1, 2)) {
                    $model->recordEvent(new ExpireCreateEvent($model->id,
                        $reg, NULL, 1, $status[$index]), ExpireWork::class);
                }
            }
        }
    }
    public function getIndexByElement($array, $element)
    {
        $index = 0;
        foreach ($array as $item) {
            if ($item == $element) {
                return $index;
            }
            $index++;
        }
        return -1;
    }
    public function addOrderPeopleEvent($respPeople, $model)
    {
        if ($respPeople[0] != NULL) {
            $respPeople = array_unique($respPeople);
            for ($i = 0; $i < count($respPeople); $i++) {
                if ($respPeople[$i] != NULL) {
                    if ($this->orderPeopleRepository->checkUnique($respPeople[$i], $model->id)) {
                        $model->recordEvent(new OrderPeopleCreateEvent($respPeople[$i], $model->id), OrderPeopleWork::class);
                    }
                }
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