<?php
namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;

class DropDownWidget extends Widget
{
    public $model; // Модель формы
    public $bringPeople; // Данные для выпадающего списка
    public $form; // Экземпляр формы, чтобы избежать конфликта

    public function run()
    {
        $this->registerAssets();

        return $this->render('DropDown', [
            'model' => $this->model,
            'bringPeople' => $this->bringPeople,
            'form' => $this->form,
        ]);
    }

    protected function registerAssets()
    {
        $js = <<<JS
        $('.add-dropdown').click(function() {
            var newDropdown = $('#dropdown-template').clone().removeAttr('id').show();
            $('#dropdown-container').append(newDropdown);
        });
        
        $(document).on('click', '.remove-dropdown', function() {
            $(this).closest('.dropdown-group').remove();
        });
        JS;
        $this->getView()->registerJs($js);
    }
}

