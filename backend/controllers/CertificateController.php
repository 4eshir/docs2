<?php

namespace backend\controllers;

use backend\builders\CertificateTemplatesBuilder;
use backend\forms\CertificateTemplatesForm;
use common\repositories\educational\CertificateTemplatesRepository;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class CertificateController extends Controller
{
    private CertificateTemplatesBuilder $builder;
    private CertificateTemplatesRepository $repository;

    public function __construct(
        $id,
        $module,
        CertificateTemplatesBuilder $builder,
        CertificateTemplatesRepository $repository,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->builder = $builder;
        $this->repository = $repository;
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $this->builder->query()
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreateTemplate()
    {
        $model = new CertificateTemplatesForm();

        if ($model->load(Yii::$app->request->post())) {
            $model->templateFile = UploadedFile::getInstance($model, 'templateFile');
            if (!is_null($model->templateFile)) {
                $model->fillEntity();
                $model->uploadTemplateFile();
                $this->repository->save($model->entity);
                return $this->redirect(['view', 'id' => $model->entity->id]);
            }
            Yii::$app->session->setFlash('danger', 'Невозможно добавить шаблон без подложки');
        }

        return $this->render('create-template', [
            'model' => $model,
        ]);
    }

    public function actionView($id)
    {
        $model = new CertificateTemplatesForm($id);
        return $this->render('view', [
            'model' => $model
        ]);
    }

    public function actionGetImage($filename)
    {
        $path = Yii::getAlias('@app/../frontend/upload/files/certificate-templates/') . $filename;
        if (file_exists($path)) {
            return Yii::$app->response->sendFile($path);
        } else {
            throw new NotFoundHttpException('File not found.');
        }
    }
}