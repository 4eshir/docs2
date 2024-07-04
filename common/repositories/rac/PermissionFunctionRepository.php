<?php

namespace common\repositories\rac;

use common\models\work\rac\PermissionTemplateFunctionWork;
use common\models\work\rac\PermissionTemplateWork;
use yii\base\InvalidValueException;
use yii\web\NotFoundHttpException;

class PermissionFunctionRepository
{
    /**
     * Возвращает все связанные с шаблоном правила или NotFoundHttpException
     * @param $templateName
     * @return array|\yii\db\ActiveRecord[]
     * @throws NotFoundHttpException
     */
    public function getTemplateLinkedPermissions($templateName)
    {
        if (array_key_exists($templateName, PermissionTemplateWork::getTemplateNames())) {
            $templateId = PermissionTemplateWork::find()->where(['name' => $templateName])->one()->id;
            return PermissionTemplateFunctionWork::find()->where(['template_id' => $templateId])->all();
        }

        throw new NotFoundHttpException("Неизвестный тип шаблона - $templateName");
    }
}