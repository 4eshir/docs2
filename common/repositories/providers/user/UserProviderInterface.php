<?php

namespace common\repositories\providers\user;

use frontend\models\work\general\UserWork;

interface UserProviderInterface
{
    public function get($id);
    public function getAll();
    public function getByUsername($username);
    public function save(UserWork $user);
}