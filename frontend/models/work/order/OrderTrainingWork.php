<?php

namespace app\models\work\order;

use common\models\scaffold\OrderMain;

class OrderTrainingWork extends OrderMain
{

    public $responsible_id;
    public $branch;
    public $scanFile;
    public $docFiles;

}