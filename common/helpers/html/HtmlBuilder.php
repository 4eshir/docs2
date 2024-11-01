<?php

namespace common\helpers\html;

use common\helpers\common\BaseFunctions;
use DomainException;
use Yii;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

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

    public static function createFilterPanel($searchModel)
    {

        /*echo '<div style="margin-bottom: 10px; margin-top: 20px">' . Html::a('Показать просроченные документы', \yii\helpers\Url::to(['document-in/index', 'sort' => '1'])) .
            ' || ' . Html::a('Показать документы, требующие ответа', \yii\helpers\Url::to(['document-in/index', 'sort' => '2'])) .
            ' || ' . Html::a('Показать все документы', \yii\helpers\Url::to(['document-in/index'])) . '</div>' */

        //var_dump($searchModel);
        $documentNumber = Html::activeTextInput($searchModel, 'realNumber', [
            'class' => 'form-control',
            'placeholder' => 'Номер документа',
            'autocomplete' => 'off',
        ]);

        //var_dump($searchModel);
        $output = '<div class="filter-panel" id="filterPanel">
        <h3>
            <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path d="M9 12L4 4H15M20 4L15 12V21L9 18V16" stroke="#009580" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg> Фильтры поиска:
        </h3>
        <div class="flexx">';

        //$form = ActiveForm::begin();
        //$output .= $form->field($searchModel, 'fullNumber');

        $output .= '<div class="form-group">';
        $output .= Html::submitButton('Поиск', ['class' => 'btn btn-primary']);
        $output .= Html::submitButton('Очистить', ['class' => 'btn btn-secondary', 'style' => 'font-weight: 500;']);
        $output .= '</div>';

        $output .= '</div></div>';
        return $output;
    }

    /**
     * Создает таблицу с данными из $dataMatrix и экшн-кнопками из $buttonMatrix
     * Первые элементы массивов $dataMatrix - названия столбцов
     * @param array $dataMatrix данные для таблицы в виде матрицы
     * @param array $buttonMatrix матрица кнопок взаимодействия класса HtmlHelper::a()
     * @param array $classes css-классы для стилизации таблицы
     * @return string
     */
    public static function createTableWithActionButtons(
        array $dataMatrix,
        array $buttonMatrix,
        array $classes = ['table' => 'table table-bordered', 'tr' => '', 'th' => '', 'td' => ''])
    {
        if (count($buttonMatrix) == 0 || count($buttonMatrix[0]) == 0) {
            return '';
        }

        $result = '<table class="' . $classes['table'] . '"><thead>';
        foreach ($dataMatrix as $row) {
            $result .= "<th class='" . $classes['th'] . "'>$row[0]</th>";
        }
        $result .= '</thead>';

        $dataMatrix = BaseFunctions::transposeMatrix($dataMatrix);
        $buttonMatrix = BaseFunctions::transposeMatrix($buttonMatrix);

        foreach ($dataMatrix as $i => $row) {
            if ($i > 0) {
                $result .= '<tr class="' . $classes['tr'] . '">';
                foreach ($row as $cell) {
                    $result .= "<td class='" . $classes['td'] . "'>$cell</td>";
                }
                foreach ($buttonMatrix[$i - 1] as $button) {
                    $result .= "<td class='" . $classes['td'] . "'>$button</td>";
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

    public static function createWarningMessage($boldMessage, $regularMessage)
    {
        return "<div class='alert alert-warning alert-dismissible fade show' role='alert' style='z-index: 1050;'>
                    <strong>$boldMessage</strong> $regularMessage
                </div>";
    }

    public static function createInfoMessage($regularMessage)
    {
        return "<div class='alert alert-info alert-dismissible fade show' role='alert' style='z-index: 1050;'>
                    $regularMessage
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
    }
}