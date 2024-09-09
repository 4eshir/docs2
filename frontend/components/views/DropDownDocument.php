<?php

use yii\helpers\ArrayHelper;

?>

<button type="button" class="add-dropdown-doc-ch">+</button>
<div id="dropdown-container-doc-ch">
    <div class="dropdown-group-doc-ch" id="dropdown-template-doc-ch" style="display:none;">
        <?php
        $params = [
            'class' => 'form-control pos',
            'prompt' => '---',
        ];
        echo $form
            ->field($model, 'executor_id[]')
            ->dropDownList(ArrayHelper::map($bringPeople, 'id', 'fullFio'), $params)
            ->label('Приказ');
        echo $form
            ->field($model, 'executor_id[]')
            ->dropDownList(ArrayHelper::map($bringPeople, 'id', 'fullFio'), $params)
            ->label('Положение');
        ?>
        <button type="button" class="remove-dropdown-doc-ch">-</button>
    </div>
</div>

