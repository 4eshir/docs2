<?php

namespace common\repositories\rac;

use common\models\scaffold\PermissionTemplateFunction;
use common\models\work\rac\PermissionTemplateFunctionWork;
use common\models\work\rac\PermissionTemplateWork;
use common\models\work\rac\UserPermissionFunctionWork;
use DomainException;
use Yii;
use yii\web\NotFoundHttpException;

class PermissionTemplateFunctionRepository
{
    public function save(PermissionTemplateFunctionWork $templateFunction)
    {
        if (!$templateFunction->save()) {
            throw new DomainException('Ошибка привязки правила к пользователю. Проблемы: '.json_encode($templateFunction->getErrors()));
        }

        return $templateFunction->id;
    }
}