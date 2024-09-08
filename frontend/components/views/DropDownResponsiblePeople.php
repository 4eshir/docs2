<?php

use yii\helpers\ArrayHelper;

?>
<button type="button" class="add-dropdown-resp">+</button>
<div id="dropdown-container-resp">
    <div class="dropdown-group-resp" id="dropdown-template-resp" style="display:none;">
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
        <button type="button" class="remove-dropdown-resp">-</button>
    </div>
</div>

