<?php

namespace common\repositories\general;

use common\models\work\general\UserWork;
use DomainException;

class UserRepository
{
    public function findByUsername($username)
    {
        return UserWork::find()->where(['username' => $username])->one();
    }

    public function save(UserWork $user)
    {
        if (!$user->save()) {
            throw new DomainException('Ошибка сохранения пользователя. Проблемы: '.json_encode($user->getErrors()));
        }

        return $user->id;
    }
}