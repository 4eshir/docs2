<?php

namespace common\helpers\html;

use Yii;
use yii\helpers\Html;

class HtmlBuilder
{
    /**
     * Метод создания массива option-s для select
     * $items должен иметь поля $id и $name
     * @param $items
     * @return string
     */
    public static function buildOptionList($items)
    {
        $result = '';
        foreach ($items as $item) {
            $result .= "<option value='" . $item->id . "'>" . $item->name . "</option>";
        }
        return $result;
    }

    public static function createEmptyOption()
    {
        return "<option value>---</option>";
    }

    /**
     * Создает таблицу разрешений на разглашение ПД
     * @param array $data
     * @return string
     */
    public static function createPersonalDataTable(array $data)
    {
        $result = "<table class='table table-bordered' style='width: 600px'>";
        foreach (Yii::$app->personalData->getList() as $key => $pd)
        {
            $result .= '<tr><td style="width: 350px">';
            if (!in_array($key, $data)) {
                $result .= $pd.'</td><td style="width: 250px"><span class="badge badge-success">Разрешено</span></td>';
            }
            else {
                $result .= $pd.'</td><td style="width: 250px"><span class="badge badge-error">Запрещено</span></td>';
            }
            $result .= '</td></tr>';
        }
        $result .= "</table>";

        return $result;
    }

    /**
     * Создает группу кнопок
     * $linksArray должен быть ассоциативным массивом ['Имя кнопки' => 'ссылка']
     * @param $linksArray
     * @return string
     */
    public static function createGroupButton($linksArray)
    {
        $result = '<div class="button-group">';
        $class = count($linksArray) < 3 ? 'btn-primary' : 'btn-secondary';
        foreach ($linksArray as $label => $url) {
            $result .= Html::a($label, $url, ['class' => $class]);
        }
        $result .= '</div>';
        return $result;
    }
}