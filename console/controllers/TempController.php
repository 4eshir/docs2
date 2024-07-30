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
        var_dump(DocumentInWork::class);
        var_dump(get_class(new DocumentInWork()));
    }
}