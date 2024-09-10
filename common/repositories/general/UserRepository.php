<?php

namespace common\repositories\general;

use common\components\traits\CommonRepositoryFunctions;
use DomainException;
use frontend\models\work\general\UserWork;

class UserRepository
{
    use CommonRepositoryFunctions;

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