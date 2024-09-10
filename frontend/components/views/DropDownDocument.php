<style>
    .bordered-div {
        border: 2px solid #000; /* Черная рамка */
        padding: 10px;          /* Отступы внутри рамки */
        border-radius: 5px;    /* Скругленные углы (по желанию) */
        margin: 10px 0;        /* Отступы сверху и снизу */
    }
</style>
<div class="bordered-div">
    <div> Изменение документов </div>
    <button type="button" class="add-dropdown-doc-ch">+</button>
    <div id="dropdown-container-doc-ch">
        <div class="dropdown-group-doc-ch" id="dropdown-template-doc-ch" style="display:none;">
            <div class="bordered-div">
                <div> Приказ </div>
                <select name="doc-1[]">
                    <option value="">Выберите</option>
                    <?php foreach ($bringPeople as $person): ?>
                        <option value="<?= htmlspecialchars($person->id) ?>">
                            <?= htmlspecialchars($person->getFullFio()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div> Положение </div>
                <select name="doc-2[]">
                    <option value="">Выберите</option>
                    <?php foreach ($bringPeople as $person): ?>
                        <option value="<?= htmlspecialchars($person->id) ?>">
                            <?= htmlspecialchars($person->getFullFio()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label>
                    <input type="radio" name="cancel" value="cancel" checked> Отмена
                </label><br>
                <label>
                    <input type="radio" name="change" value="change"> Изменение
                </label><br>
                <button type="button" class="remove-dropdown-doc-ch">-</button>
            </div>
        </div>
    </div>
</div>




