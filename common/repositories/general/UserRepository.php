<?php

namespace common\repositories\general;

use common\components\traits\CommonDatabaseFunctions;
use DomainException;
use frontend\models\work\general\UserWork;

class UserRepository
{
    use CommonDatabaseFunctions;

    public function get($id)
    {
        return UserWork::find()->where(['id' => $id])->one();
    }

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