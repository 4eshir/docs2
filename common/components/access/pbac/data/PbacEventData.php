<?php

namespace common\components\access\pbac\data;

use frontend\models\work\general\UserWork;

class PbacEventData extends PbacData
{
    public UserWork $user;

    /** @var int[] $branches */
    public array $branches;

    public function __construct(
        UserWork $user,
        array $branches
    )
    {
        $this->user = $user;
        $this->branches = $branches;
    }
}