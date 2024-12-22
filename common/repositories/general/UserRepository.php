<?php

namespace common\repositories\general;

use common\components\traits\CommonDatabaseFunctions;
use common\repositories\providers\user\UserProvider;
use common\repositories\providers\user\UserProviderInterface;
use frontend\models\work\general\UserWork;
use Yii;

class UserRepository
{
    use CommonDatabaseFunctions;

    private $userProvider;

    public function __construct(UserProviderInterface $userProvider = null)
    {
        if (!$userProvider) {
            $userProvider = new UserProvider();
        }

        $this->userProvider = $userProvider;
    }

    public function get($id)
    {
        return $this->userProvider->get($id);
    }

    public function getAll()
    {
        return $this->userProvider->getAll();
    }

    public function findByUsername($username)
    {
        return $this->userProvider->getByUsername($username);
    }

    public function changePassword($password, $userId)
    {
        $passwordHash = Yii::$app->security->generatePasswordHash($password);
        /** @var UserWork $user */
        $user = $this->get($userId);

        if ($user) {
            $user->setPassword($passwordHash);
            $this->save($user);
        }
    }

    public function save(UserWork $user)
    {
        return $this->userProvider->save($user);
    }
}