<?php

namespace console\controllers;

use common\repositories\general\PeopleRepository;
use frontend\events\document_in\InOutDocumentCreateEvent;
use common\models\scaffold\People;
use common\models\work\document_in_out\DocumentInWork;
use common\models\work\document_in_out\InOutDocumentsWork;
use common\models\work\general\PeopleWork;
use common\services\monitoring\PermissionLinksMonitor;
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