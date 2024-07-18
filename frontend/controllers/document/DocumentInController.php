<?php

namespace frontend\controllers\document;

use common\helpers\FilesHelper;
use common\models\search\SearchDocumentIn;
use common\repositories\document_in_out\DocumentInRepository;
use common\services\general\files\FileService;
use Yii;
use yii\web\Controller;

class DocumentInController extends Controller
{
    private DocumentInRepository $repository;
    private FileService $fileService;

    public function __construct(
        $id,
        $module,
        DocumentInRepository $repository,
        FileService $fileService,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->repository = $repository;
        $this->fileService = $fileService;
    }

    public function actionIndex()
    {
        $searchModel = new SearchDocumentIn();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->repository->get($id)
        ]);
    }

    public function actionGetFile($filepath)
    {
        $data = $this->fileService->downloadFile($filepath);
        if ($data['type'] == FilesHelper::FILE_SERVER) {
            Yii::$app->response->sendFile($data['obj']->file);
        }
        else {
            $fp = fopen('php://output', 'r');
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . FilesHelper::getFilenameFromPath($data['obj']->filepath));
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . $data['obj']->file->size);

            $data['obj']->file->download($fp);

            fseek($fp, 0);
        }
    }
}