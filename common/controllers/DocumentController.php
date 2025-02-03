<?php

namespace common\controllers;

use common\helpers\files\FilesHelper;
use common\repositories\general\FilesRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\events\general\FileDeleteEvent;
use frontend\helpers\HeaderWizard;
use frontend\models\work\general\FilesWork;
use Hidehalo\Nanoid\Client;
use Yii;
use yii\web\Controller;
use ZipArchive;

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

    public function actionGetFiles(array $filepaths)
    {
        $zipFileName = 'files.zip';

        $tempFile = tempnam(sys_get_temp_dir(), 'zip_' . (Yii::createObject(Client::class))->generateId(21));
        if ($tempFile === false) {
            throw new \RuntimeException('Не удалось создать временный файл.');
        }

        $zip = new ZipArchive();
        if ($zip->open($tempFile, ZipArchive::CREATE) !== true) {
            unlink($tempFile);
            throw new \RuntimeException('Не удалось создать архив.');
        }

        foreach ($filepaths as $path) {
            $fileData = $this->fileService->downloadFile($path);

            if ($fileData['type'] == FilesHelper::FILE_SERVER) {
                $filename = FilesHelper::getFilenameFromPath($path);
                $zip->addFile($fileData['obj']->file, $filename);
            } else {
                $filename = FilesHelper::getFilenameFromPath($fileData['obj']->filepath);
                $content = file_get_contents($fileData['obj']->file);
                $zip->addFromString($filename, $content);
            }
        }

        $zip->close();
        Yii::$app->response->sendFile($tempFile, $zipFileName);
        unlink($tempFile);
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