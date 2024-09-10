<?php

namespace common\repositories\rac;

use DomainException;
use frontend\models\work\rac\PermissionFunctionWork;
use frontend\models\work\rac\PermissionTemplateWork;
use frontend\models\work\rac\UserPermissionFunctionWork;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class UserPermissionFunctionRepository
{
    private PermissionFunctionRepository $permissionFunctionRepository;

    public function __construct(PermissionFunctionRepository $permissionFunctionRepository)
    {
        $this->permissionFunctionRepository = $permissionFunctionRepository;
    }

    public function attachTemplatePermissionsToUser($templateName, $userId, $branch)
    {
        if (array_key_exists($templateName, PermissionTemplateWork::getTemplateNames()) &&
            (array_key_exists($branch, Yii::$app->branches->getList()) || $branch == null)) {
            $functions = $this->permissionFunctionRepository->getTemplateLinkedPermissions($templateName);

            foreach ($functions as $function) {
                $this->save(
                    UserPermissionFunctionWork::fill(
                        $userId,
                        $function->id,
                        Yii::$app->branches->get($branch)
                    )
                );
            }

            return true;
        }

        throw new NotFoundHttpException("Неизвестный тип шаблона - $templateName или неизвестный отдел - $branch");
    }

    /**
     * Возвращает список PermissionFunctionWork для пользователя с ID = userId
     * @param $userId
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getPermissionsByUser($userId)
    {
        $userPermissions = ArrayHelper::getColumn(UserPermissionFunctionWork::find()->where(['user_id' => $userId])->all(), 'function_id');
        return PermissionFunctionWork::find()->where(['IN', 'id', $userPermissions])->all();
    }

    public function save(UserPermissionFunctionWork $userFunction)
    {
        if (!$userFunction->save()) {
            throw new DomainException('Ошибка привязки правила к пользователю. Проблемы: '.json_encode($userFunction->getErrors()));
        }

        return $userFunction->id;
    }
}