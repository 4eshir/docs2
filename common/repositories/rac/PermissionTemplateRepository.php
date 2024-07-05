<?php

namespace common\repositories\rac;

use common\models\work\rac\PermissionTemplateWork;
use common\models\work\rac\UserPermissionFunctionWork;
use DomainException;
use Yii;
use yii\web\NotFoundHttpException;

class PermissionTemplateRepository
{
    public function save(PermissionTemplateWork $template)
    {
        if (!$template->save()) {
            throw new DomainException('Ошибка привязки правила к пользователю. Проблемы: '.json_encode($template->getErrors()));
        }

        return $template->id;
    }
}