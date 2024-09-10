<?php

namespace common\repositories\rac;

use DomainException;
use frontend\models\work\rac\PermissionTemplateWork;

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