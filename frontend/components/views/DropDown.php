<?php

use yii\helpers\ArrayHelper;

?>

<div id="dropdown-container">
    <div class="dropdown-group" id="dropdown-template" style="display:none;">
        <?php
        $params = [
            'class' => 'form-control pos',
            'prompt' => '---',
        ];
        echo $form
            ->field($model, 'executor_id[]')
            ->dropDownList(ArrayHelper::map($bringPeople, 'id', 'fullFio'), $params)
            ->label('Ответственные');
        ?>
        <button type="button" class="remove-dropdown">-</button>
    </div>
</div>

<button type="button" class="add-dropdown">+</button>