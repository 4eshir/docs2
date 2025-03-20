<?php

namespace backend\services;

use backend\forms\TokensForm;
use common\helpers\common\SqlHelper;
use common\repositories\rubac\PermissionTokenRepository;
use common\repositories\rubac\UserPermissionFunctionRepository;
use frontend\models\work\rubac\PermissionTokenWork;
use Yii;
use yii\db\Exception;

class PermissionTokenService
{
    private PermissionTokenRepository $repository;
    private UserPermissionFunctionRepository $userPermissionRepository;

    public function __construct(
        PermissionTokenRepository $repository,
        UserPermissionFunctionRepository $userPermissionRepository
    )
    {
        $this->repository = $repository;
        $this->userPermissionRepository = $userPermissionRepository;
    }

    public function saveToken(TokensForm $form)
    {
        if (!$this->checkDuplicate($form)) {
            Yii::$app->session->setFlash('danger', 'У данного пользователя уже есть данное разрешение');
            return;
        }

        $currentTime = date('Y-m-d H:i:s');
        $model = PermissionTokenWork::fill(
            $form->userId,
            $form->permissionId,
            $currentTime,
            date('Y-m-d H:i:s', strtotime($currentTime) + $form->duration * 3600),
            $form->branch
        );

        $this->repository->save($model);

        $this->addDeleteEventForToken($model);
    }

    public function checkDuplicate(TokensForm $form)
    {
        $duplicate = $this->repository->findByUserFunctionBranch(
            $form->userId,
            $form->permissionId,
            $form->branch
        );

        $duplicateFromPermissions = $this->userPermissionRepository->getByUserPermissionBranch(
            $form->userId,
            $form->permissionId,
            $form->branch
        );

        return is_null($duplicate) && is_null($duplicateFromPermissions);
    }

    public function addDeleteEventForToken(PermissionTokenWork $model)
    {
        $deleteEvent = SqlHelper::createDeleteEvent(
            "token_delete_$model->id",
            $model->end_time,
            'permission_token',
            "WHERE `id`= $model->id"
        );

        try {
            Yii::$app->db->createCommand($deleteEvent)->execute();
        } catch (Exception $e) {
            Yii::error("Ошибка выполнения команды: " . $e->getMessage());
        }
    }
}