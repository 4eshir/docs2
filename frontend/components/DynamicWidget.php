<?php

namespace app\components;

use yii\base\Widget;

class DynamicWidget extends Widget
{
    public $widgetContainer;
    public $widgetBody;
    public $widgetItem;
    public $model;
    public $formId;
    public $formFields;

    public function init()
    {
        parent::init();
        if ($this->widgetContainer === null) {
            throw new \InvalidArgumentException('widgetContainer must be set.');
        }
        if ($this->widgetBody === null) {
            throw new \InvalidArgumentException('widgetBody must be set.');
        }
        if ($this->widgetItem === null) {
            throw new \InvalidArgumentException('widgetItem must be set.');
        }
        if ($this->model === null) {
            throw new \InvalidArgumentException('model must be set.');
        }
        if ($this->formId === null) {
            throw new \InvalidArgumentException('formId must be set.');
        }
    }

    public function run()
    {
        $this->registerAssets();
        $this->script();
        return $this->render('Dynamic', [
            'model' => $this->model,
            'widgetContainer' => $this->widgetContainer,
            'widgetBody' => $this->widgetBody,
            'formId' => $this->formId,
            'formFields' => $this->formFields,
        ]);
    }

    public function registerAssets()
    {
        DynamicWidgetAsset::register($this->getView());
    }
    public function script()
    {
        $script = <<< JS
        var index = 1; // Инициализируем с 1, так как у нас уже есть один элемент с id=item1
        $('.add-item').click(function() {
            var container = $(this).closest('.container-items');
            var item = $('.item:last', container).clone();
            index++; // Увеличиваем счетчик для нового ID
            item.attr('id', 'item' + index); // Назначаем новый ID
            item.find('input').val(''); // Очистить поля ввода
            container.append(item);
        });
        
        $('.container-items').on('click', '.remove-item', function() {
            var container = $(this).closest('.container-items');
            if (container.children('.item').length > 1) {
                $(this).closest('.item').remove();
            }
        });
        JS;
        $this->getView()->registerJs($script);
    }
}
