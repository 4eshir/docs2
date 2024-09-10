<?php

namespace common\controllers;

use common\helpers\files\FilesHelper;
use common\repositories\general\FilesRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\events\general\FileDeleteEvent;
use frontend\helpers\HeaderWizard;
use frontend\models\work\general\FilesWork;
use Yii;
use yii\web\Controller;

/**
 * Контроллер, хранящий в себе общий для всего документооборота функционал
 * Рекомендуется наследоваться от него при реализации частей ЭДО
 */
class DocumentController extends Controller
{
    private FileService $fileService;
    private FilesRepository $filesRepository;

    public function __construct(
        $id,
        $module,
        FileService $fileService,
        FilesRepository $filesRepository,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->fileService = $fileService;
        $this->filesRepository = $filesRepository;
    }

    public function actionGetFile($filepath)
    {
        $data = $this->fileService->downloadFile($filepath);
        if ($data['type'] == FilesHelper::FILE_SERVER) {
            Yii::$app->response->sendFile($data['obj']->file);
        }
        else {
            $fp = fopen('php://output', 'r');
            HeaderWizard::setFileHeaders(FilesHelper::getFilenameFromPath($data['obj']->filepath), $data['obj']->file->size);
            $data['obj']->file->download($fp);
            fseek($fp, 0);
        }
    }

    public function actionDeleteFile($modelId, $fileId)
    {
        try {
            $file = $this->filesRepository->getById($fileId);

            /** @var FilesWork $file */
            $filepath = $file ? basename($file->filepath) : '';
            $this->fileService->deleteFile(FilesHelper::createAdditionalPath($file->table_name, $file->file_type) . $file->filepath);
            $file->recordEvent(new FileDeleteEvent($fileId), get_class($file));
            $file->releaseEvents();

            Yii::$app->session->setFlash('success', "Файл $filepath успешно удален");
            return $this->redirect(['update', 'id' => $modelId]);
        }
        catch (DomainException $e) {
            return $e->getMessage();
        }
    }
}