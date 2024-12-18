<?php

namespace frontend\models\work\educational\training_group;

use common\models\scaffold\TrainingGroupExpert;
use common\models\scaffold\TrainingGroupLesson;
use common\repositories\dictionaries\AuditoriumRepository;
use frontend\models\work\dictionaries\AuditoriumWork;
use Yii;

class TrainingGroupExpertWork extends TrainingGroupExpert
{
    const TYPE_EXTERNAL = 1;
    const TYPE_INTERNAL = 2;
}