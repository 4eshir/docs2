<?php

namespace app\components;



use yii\base\Widget;
use yii\helpers\Url;

class GroupParticipantWidget extends Widget
{
    public $config;
    public $model;
    public $dataProviderGroup;
    public $dataProviderParticipant;
    public $nomenclature;
    public $transferGroups;
    public function init()
    {
        parent::init();
        if ($this->config == NULL) {
            throw new \InvalidArgumentException('Config must be set.');
        }
        if ($this->config['participantUrl'] == NULL) {
            throw new \InvalidArgumentException('Url participant must be set.');
        }
        if ($this->config['groupUrl'] == NULL) {
            throw new \InvalidArgumentException('Url group must be set.');
        }
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
        $this->groupParticipantScript($this->config['participantUrl']);
        $this->groupScript($this->config['groupUrl']);
    }
    public function groupScript($groupUrl){
        //группы 'order/order-training/get-group-by-branch'
        $this->getView()->registerJs("$('#branch-dropdown').on('change', function() {
        var branchId = $(this).val();
        $.ajax({
            url:'" . Url::to([$groupUrl]) . "',
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
    }
    public function groupParticipantScript($participantUrl){
        //участники 'get-group-participants-by-branch'
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
                url: '" . Url::to([$participantUrl]) . "', // Укажите ваш правильный путь к контроллеру
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
        window.onload = function () {
            const checkedCheckboxes = $('.group-checkbox:checked'); 
            const groupIds = [];
            var number = $('#order-number-dropdown').val();
            var modelId = " . $modelId . ";
            checkedCheckboxes.each(function () {
                groupIds.push($(this).data('id')); // Собираем ID всех выбранных чекбоксов
            });  
            $.ajax({
                url: '" . Url::to([$participantUrl]) . "', // Укажите ваш правильный путь к контроллеру
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