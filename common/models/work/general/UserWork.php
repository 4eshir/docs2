<?php

namespace common\models\work\general;

use common\models\scaffold\User;

class UserWork extends User
{
    public static function fill(
        string $firstname,
        string $secondname,
        string $username,
        string $passwordHash,
        string $email,
        string $patronymic = null,
        string $authKey = null,
        string $passwordResetToken = null,
        int $aka = null
    )
    {
        $entity = new static();
        $entity->firstname = $firstname;
        $entity->secondname = $secondname;
        $entity->patronymic = $patronymic;
        $entity->username = $username;
        $entity->password_hash = $passwordHash;
        $entity->email = $email;
        $entity->auth_key = $authKey;
        $entity->password_reset_token = $passwordResetToken;
        $entity->aka = $aka;

        return $entity;
    }
}