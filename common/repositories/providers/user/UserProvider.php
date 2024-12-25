<?php

namespace common\repositories\providers\user;

use DomainException;
use frontend\models\work\general\UserWork;

class UserProvider implements UserProviderInterface
{
    public function get($id)
    {
        return UserWork::find()->where(['id' => $id])->one();
    }

    public function getAll()
    {
        return UserWork::find()->all();
    }

    public function getByUsername($username)
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