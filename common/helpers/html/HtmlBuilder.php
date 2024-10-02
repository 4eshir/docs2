<?php

namespace common\helpers\html;

use common\helpers\common\BaseFunctions;
use DomainException;
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
     * Создает таблицу с данными из $dataMatrix и экшн-кнопками из $buttonMatrix
     * Первые элементы массивов $dataMatrix - названия столбцов
     * @param array $dataMatrix данные для таблицы в виде матрицы
     * @param array $buttonMatrix матрица кнопок взаимодействия класса HtmlHelper::a()
     * @return string
     */
    public static function createTableWithActionButtons(array $dataMatrix, array $buttonMatrix)
    {
        if (count($buttonMatrix) == 0 || count($buttonMatrix[0]) == 0) {
            return '';
        }

        $result = '<table class="table table-bordered"><thead>';
        foreach ($dataMatrix as $row) {
            $result .= "<th>$row[0]</th>";
        }
        $result .= '</thead>';

        $dataMatrix = BaseFunctions::transposeMatrix($dataMatrix);
        $buttonMatrix = BaseFunctions::transposeMatrix($buttonMatrix);

        foreach ($dataMatrix as $i => $row) {
            if ($i > 0) {
                $result .= '<tr>';
                foreach ($row as $cell) {
                    $result .= "<td>$cell</td>";
                }
                foreach ($buttonMatrix[$i - 1] as $button) {
                    $result .= "<td>$button</td>";
                }
                $result .= '</tr>';
            }
        }

        $result .= '</table>';

        return $result;
    }

    /**
     * Создает массив кнопок с указанными в $queryParams параметрами
     * @param string $text имя кнопок
     * @param string $url url кнопок
     * @param array $queryParams массив параметров вида ['param_name' => [1, 2, 3], 'param_name' => ['some', 'data'], ...]
     * @return array
     */
    public static function createButtonsArray(string $text, string $url, array $queryParams)
    {
        $result = [];

        $keys = array_keys($queryParams);
        $maxLength = max(array_map('count', $queryParams));

        // Формируем результирующий массив
        for ($i = 0; $i < $maxLength; $i++) {
            $combined = [];
            foreach ($keys as $key) {
                if (isset($queryParams[$key][$i])) {
                    $combined[$key] = $queryParams[$key][$i];
                }
            }
            if (!empty($combined)) {
                $result[] = Html::a($text, array_merge([$url], $combined));
            }
        }

        return $result;
    }

}