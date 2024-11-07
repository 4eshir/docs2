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
        return "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                    <strong>$boldMessage</strong> $regularMessage
                </div>";
    }

    public static function createInfoMessage($regularMessage)
    {
        return "<div class='alert alert-info alert-dismissible fade show' role='alert'>
                    $regularMessage
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
    }

    public static function createFileIconDownload($link)
    {
        if (empty($link))
        {
            return '<svg version="1.1" id="Слой_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 36.67 46.59" style="enable-background:new 0 0 36.67 46.59;" xml:space="preserve">
<style type="text/css">
	.st0{fill:#020202;}
	.st1{fill:#060606;}
	.st2{fill:#040404;}
	.st3{fill:#0D0D0D;}
	.st4{fill:#010101;}
	.st5{fill:#030303;}
	.st6{fill:#050505;}
	.st7{fill:none;stroke:#000000;stroke-width:1.7;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;}
	.st8{fill:none;stroke:#000000;stroke-width:1.7;stroke-miterlimit:10;}
	.st9{fill:none;stroke:#000000;stroke-width:1.7;stroke-linecap:round;stroke-miterlimit:10;}
</style>
<g>
	<path class="st7" d="M35.77,8.89v34.34c0,1.39-1.13,2.51-2.51,2.51H3.37c-1.39,0-2.51-1.13-2.51-2.51V3.36
		c0-1.39,1.13-2.51,2.51-2.51h24.41c0.25,0,0.49,0.1,0.67,0.28l7.05,7.1C35.67,8.4,35.77,8.64,35.77,8.89z"/>
	<path class="st8" d="M34.98,8.98h-5.09c-1.39,0-2.51-1.13-2.51-2.51V1.32"/>
	<circle class="st8" cx="18.34" cy="22.82" r="11.33"/>
	<line class="st8" x1="25.5" y1="14.04" x2="11.24" y2="31.66"/>
	<line class="st9" x1="7.01" y1="38.29" x2="29.73" y2="38.29"/>
</g>
</svg>';
        }
        return "";
    }
}