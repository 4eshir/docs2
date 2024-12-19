<?php

namespace frontend\helpers\document;

use common\helpers\html\HtmlBuilder;
use Yii;
use yii\helpers\Url;

class DocumentOutHelper
{
    public static function createGroupButton()
    {
        $links = [
            'Добавить документ' => Url::to([Yii::$app->frontUrls::DOC_OUT_CREATE]),
            'Добавить резерв' => Url::to([Yii::$app->frontUrls::DOC_OUT_RESERVE]),
        ];
        return HtmlBuilder::createGroupButton($links);
    }
}