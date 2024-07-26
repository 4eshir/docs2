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
        $repository = Yii::createObject(PeopleRepository::class);
        var_dump($repository->getPeopleFromMainCompany());
    }
}