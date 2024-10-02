<?php

namespace frontend\models\work\educational;

use common\models\scaffold\AuthorProgram;
use frontend\models\work\general\PeopleWork;

class AuthorProgramWork extends AuthorProgram
{
    public function getAuthorWork()
    {
        return $this->hasOne(PeopleWork::class, ['id' => 'author_id']);
    }
}