<?php

namespace frontend\models\work\dictionaries;

use common\models\scaffold\Position;

class PositionWork extends Position
{
    public function getPos(){
        return $this->name;
    }
}
