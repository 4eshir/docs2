<?php

namespace app\components;



use yii\base\Widget;
use yii\helpers\Url;

class GroupParticipantWidget extends Widget
{
    public $model;
    public $dataProviderGroup;
    public $dataProviderParticipant;
    public $nomenclature;
    public $transferGroups;
    public $config;
    public function init()
    {
        //обработка config-а
    }
    public function run(){
        $this->script();
        return $this->render('groupParticipant',
        [
            'dataProviderGroup' => $this->dataProviderGroup,
            'model' => $this->model,
            'dataProviderParticipant' => $this->dataProviderParticipant,
            'nomenclature' => $this->nomenclature,
            'transferGroups' => $this->transferGroups,
        ]);
    }

    public function script()
    {
        if($this->model->id != NULL){
            $modelId = $this->model->id;
        }
        else {
            $modelId = 0;
        }
        $this->getView()->registerJs("
        $(document).on('change', '.group-checkbox', function () {
            const checkedCheckboxes = $('.group-checkbox:checked'); 
            const groupIds = [];
            var number = $('#order-number-dropdown').val();
            var modelId = " . $modelId . ";
            checkedCheckboxes.each(function () {
                groupIds.push($(this).data('id')); // Собираем ID всех выбранных чекбоксов
            });  
            $.ajax({
                url: '" . Url::to(['get-group-participants-by-branch']) . "', // Укажите ваш правильный путь к контроллеру
                type: 'GET',
                data: { 
                    groupIds: JSON.stringify(groupIds), 
                    modelId: modelId, 
                    nomenclature: number
                }, // Отправляем массив ID
                success: function (data) {
                    var gridView = $('.training-group-participant .grid-view');
                    gridView.html(data.gridHtml); // Обновляем HTML GridView
                },
                error: function() {
                    alert('Ошибка при загрузке данных.');
                }
            });
        });");
        $this->getView()->registerJs("
            $('#branch-dropdown').on('change', function() {
                var branchId = $(this).val();
                $.ajax({
                    url: '" . Url::to(['order/order-training/get-list-by-branch']) . "', // Укажите ваш правильный путь к контроллеру
                    type: 'GET',
                    data: { branch_id: branchId },
                    success: function(data) {
                        var options;
                        options = '<option value=\"\">---</option>';
                        $.each(data, function(index, value) {
                            options += '<option value=\"' + index + '\">' + value + '</option>';
                        });
                        $('#order-number-dropdown').html(options); // Обновляем второй выпадающий список
                    }
                });
            });
        ");
    $this->getView()->registerJs("$('#branch-dropdown').on('change', function() {
        var branchId = $(this).val();
        $.ajax({
            url:'" . Url::to(['order/order-training/get-group-by-branch']) . "',
            type: 'GET',
            data: { branch: branchId },
            success: function(data) {
               
                var gridView = $('.training-group .grid-view'); 
                console.log(gridView);
                gridView.html(data.gridHtml); // Обновляем HTML GridView
            },
            error: function() {
                alert('Ошибка при загрузке данных.');
            }
        });
    });");
    $this->getView()->registerJs("
        window.onload = function () {
            const checkedCheckboxes = $('.group-checkbox:checked'); 
            const groupIds = [];
            var number = $('#order-number-dropdown').val();
            var modelId = " . $modelId . ";
            checkedCheckboxes.each(function () {
                groupIds.push($(this).data('id')); // Собираем ID всех выбранных чекбоксов
            });  
            $.ajax({
                url: '" . Url::to(['get-group-participants-by-branch']) . "', // Укажите ваш правильный путь к контроллеру
                type: 'GET',
                data: { 
                    groupIds: JSON.stringify(groupIds), 
                    modelId: modelId, 
                    nomenclature:  number
                }, // Отправляем массив ID
                success: function (data) {
                    var gridView = $('.training-group-participant .grid-view');
                    gridView.html(data.gridHtml); // Обновляем HTML GridView
                },
                error: function() {
                    alert('Ошибка при загрузке данных.');
                }
            });
        };
    ");
    }
}