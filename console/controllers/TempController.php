<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;

class TempController extends Controller
{
    public function actionCheck()
    {
        var_dump(Yii::$app->basePath . '/upload/files/document-in/docs/Ред5_Вх.20240730_233_РШТ_.docx');
        var_dump(file_exists(Yii::$app->basePath . '/upload/files/document-in/docs/Ред5_Вх.20240730_233_РШТ_.docx'));
    }
}