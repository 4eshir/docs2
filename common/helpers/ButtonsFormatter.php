<?php

namespace common\helpers;

use yii\helpers\Url;

class ButtonsFormatter
{
    public static function UpdateDeleteLinks ($id) {
        return [
            'Редактировать' => [
                'url' => ['update', 'id' => $id],
                'class' => 'btn-primary',
            ],
            'Удалить' => [
                'url' => ['delete', 'id' => $id],
                'class' => 'btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены, что хотите удалить этот элемент?',
                    'method' => 'post',
                ],
            ],
        ];
    }

    public static function TwoPrimaryLinks ($linkFirst, $linkSecond) {
        return [
            'Добавить документ' => [
                'url' => Url::to([$linkFirst]),
                'class' => 'btn-primary'
            ],
            'Добавить резерв' => [
                'url' => Url::to([$linkSecond]),
                'class' => 'btn-primary'
            ],
        ];
    }

    public static function PrimaryLinkAndModal ($link, $targetNameModal) {
        return [
            'Добавить документ' => [
                'url' => Url::to([$link]),
                'class' => 'btn-primary'
            ],
            'Добавить резерв' => [
                'id' => 'open-modal-reserve',
                'url' => '#',
                'class' => 'btn-primary',
                'data' => [
                    'toggle' => 'modal',
                    'target' => $targetNameModal,
                ],
            ],
        ];
    }
}