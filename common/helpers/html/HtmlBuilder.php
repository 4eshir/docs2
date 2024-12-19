<?php

namespace common\helpers\html;

use common\helpers\common\BaseFunctions;
use DomainException;
use DOMDocument;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;
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

    public static function filterButton($resetUrl) {
        return '<div class="form-group-button">
                    <button type="submit" class="btn btn-primary">Поиск</button>
                    <a href="'.Url::to([$resetUrl]).'" type="reset" class="btn btn-secondary" style="font-weight: 500;">Очистить</a>
                </div>';
    }

    /**
     * Создает панель фильтров на _search страницах. Обязательно наличие HtmlCreator::filterToggle() на странице отображения (index)
     * @param $searchModel
     * @param $searchFields
     * @param ActiveForm $form
     * @param $valueInRow   // количество элементов поиска в строке
     * @param $resetUrl // является кнопкой сброса фильтров
     * @return string
     */
    public static function createFilterPanel($searchModel, $searchFields, ActiveForm $form, $valueInRow, $resetUrl)
    {
        $result = '<div class="filter-panel" id="filterPanel">
                        '.HtmlCreator::filterHeaderForm().'
                        <div class="filter-date">';
        $counter = 0;
        foreach ($searchFields as $attribute => $field) {
            if ($counter % $valueInRow == 0) {
                $result .= '<div class="flexx">';
            }
            $counter++;

            $result .= '<div class="filter-input">';
            $options = [
                'placeholder' => $field['placeholder'] ?? '',
                'class' => 'form-control',
                'autocomplete' => 'off',
            ];
            if ($field['type'] === 'date') {
                $widgetOptions = [
                    'dateFormat' => $field['dateFormat'],
                    'language' => 'ru',
                    'options' => $options,
                    'clientOptions' => $field['clientOptions'],
                ];
                $result .= $form->field($searchModel, $attribute)->widget(DatePicker::class, $widgetOptions)->label(false);
            } elseif ($field['type'] === 'text') {
                $result .= $form->field($searchModel, $attribute)->textInput($options)->label(false);
            } elseif ($field['type'] === 'dropdown') {
                $options['prompt'] = $field['prompt'];
                $options['options'] = $field['options'];
                //$options['options'] = [$searchModel->$attribute => ['Selected' => true]];
                $result .= $form->field($searchModel, $attribute)->dropDownList($field['data'], $options)->label(false);
            }
            $result .= '</div>';
            if ($counter % $valueInRow == 0) {
                $result .= '</div>';
            }
        }
        $result .= self::filterButton($resetUrl) . '</div>
            </div>';
        return $result;
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

    /**
     * Добавляет столбец чекбоксов к таблице
     * @param string $formAction экшн для формы
     * @param string $submitContent текст кнопки сабмита
     * @param string $checkName имя для полей формы (чекбоксов)
     * @param array $checkValues массив значений для чекбоксов
     * @param string $table исходная таблица
     * @param array $classes массив классов для стилизации формата ['submit' => 'classname']
     * @return string
     */
    public static function wrapTableInCheckboxesColumn(
        string $formAction,
        string $submitContent,
        string $checkName,
        array $checkValues,
        string $table,
        array $classes = ['submit' => 'btn btn-success']
    ) {
        // Находим все строки таблицы
        preg_match_all('/<tr[^>]*>(.*?)<\/tr>/s', $table, $matches);
        $rows = $matches[0];

        // Создаем массив чекбоксов
        $checkboxes = [];
        foreach ($checkValues as $key => $value) {
            $checkboxes[$key] = "<input type='hidden' name='$checkName' value='0'>".
                "<input type='checkbox' id='traininggroupwork-delarr$key' class='check' name='$checkName' value='$value'>";

            // Добавляем чекбокс в начало каждой строки
            $rows[$key] = preg_replace('/<tr[^>]*>/', "<tr><td>$checkboxes[$key]</td>", $rows[$key]);
        }

        $newHtmlTable = str_replace($matches[0], $rows, $table);

        preg_match_all('/<thead[^>]*>(.*?)<\/thead>/s', $newHtmlTable, $matches);
        $thead = $matches[0][0];
        $newTh = '<th class=""><input type="checkbox" class="checkbox-group"></th>';
        $newHtmlString = str_replace('<thead>', '<thead>' . $newTh, $thead);
        $newHtmlTable = preg_replace('/(<thead>.*?<\/thead>)/s', $newHtmlString, $newHtmlTable);

        $newClass = 'table-checkbox';
        $newHtmlString = preg_replace_callback(
            '/<table([^>]*)>/i',
            function ($matches) use ($newClass) {
                $attributes = $matches[1]; // Содержимое между <table и >

                // Если атрибут class уже существует
                if (preg_match('/class\s*=\s*"([^"]*)"/i', $attributes, $classMatch)) {
                    $classes = explode(' ', trim($classMatch[1]));

                    // Добавляем новый класс, если его еще нет
                    if (!in_array($newClass, $classes)) {
                        $classes[] = $newClass;
                    }

                    // Обновляем атрибут class
                    $updatedAttributes = preg_replace('/class\s*=\s*"[^"]*"/i', 'class="' . implode(' ', $classes) . '"', $attributes);

                    return '<table' . $updatedAttributes . '>';
                } else {
                    // Если атрибута class нет, добавляем его
                    return '<table' . $attributes . ' class="' . $newClass . '">';
                }
            },
            $newHtmlTable
        );

        $newHtmlTable = $newHtmlString;

        return self::wrapInForm($formAction, $submitContent, $newHtmlTable, $classes);
    }

    /**
     * Оборачивает в форму какой-либо контент
     * @param string $formAction экшн формы
     * @param string $submitContent текст кнопки сабмита
     * @param string $content контент, который необходимо обернуть в форму
     * @param array $classes массив классов для стилизации формата ['submit' => 'classname']
     * @return string
     */
    public static function wrapInForm(
        string $formAction,
        string $submitContent,
        string $content,
        array $classes = ['submit' => 'btn btn-success']
    )
    {
        $csrfToken = Yii::$app->request->getCsrfToken();
        $result = "<form action='$formAction' method='post'>";
        $result .=  Html::hiddenInput('_csrf-frontend', $csrfToken);
        $result .= $content;
        $result .= Html::submitButton($submitContent, ['class' => $classes['submit']]);
        $result .= "</form>";

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

    public static function createGroupFilesInViewCard($link, $text)
    {
        $link = ArrayHelper::getColumn($link, 'link');
        //var_dump($link[0]);
        $result = '<div class="fileIcon">';
        if (empty($link))
        {
            $result .= '
                <svg version="1.1" id="Слой_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
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
                <title>Файл отсуствует</title>
                <g>
                    <path class="st7" d="M35.77,8.89v34.34c0,1.39-1.13,2.51-2.51,2.51H3.37c-1.39,0-2.51-1.13-2.51-2.51V3.36
                        c0-1.39,1.13-2.51,2.51-2.51h24.41c0.25,0,0.49,0.1,0.67,0.28l7.05,7.1C35.67,8.4,35.77,8.64,35.77,8.89z"/>
                    <path class="st8" d="M34.98,8.98h-5.09c-1.39,0-2.51-1.13-2.51-2.51V1.32"/>
                    <circle class="st8" cx="18.34" cy="22.82" r="11.33"/>
                    <line class="st8" x1="25.5" y1="14.04" x2="11.24" y2="31.66"/>
                    <line class="st9" x1="7.01" y1="38.29" x2="29.73" y2="38.29"/>
                </g>
                </svg>' . "<div>{$text}</div>";
        }
        else
        {
            $result .= '<a href="#">'.'<svg version="1.1" id="Слой_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve">
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
	<path class="st0" d="M26.95,0.05c1.14,0.23,1.91,0.99,2.68,1.78c1.9,1.93,3.82,3.85,5.76,5.75c0.88,0.86,1.28,1.85,1.27,3.08
		c-0.03,2.92-0.01,5.85-0.01,8.77c0,0.18,0,0.35,0,0.59c0.21,0,0.38,0,0.55,0c2.78,0,5.55,0,8.33,0c2.68,0,4.42,1.74,4.43,4.41
		c0,7.03,0,14.06,0,21.09c0,2.68-1.76,4.43-4.46,4.43c-7,0-14,0-21,0c-2.29,0-3.75-1.09-4.42-3.32c-0.16,0-0.34,0-0.52,0
		c-5.26,0-10.52,0-15.78,0c-1.93,0-2.95-0.71-3.63-2.53c-0.01-0.04-0.06-0.06-0.1-0.09c0-13.78,0-27.55,0-41.33
		c0.07-0.14,0.14-0.27,0.19-0.42c0.31-0.82,0.84-1.45,1.62-1.84c0.29-0.15,0.61-0.25,0.91-0.38C10.83,0.05,18.89,0.05,26.95,0.05z
		 M20.02,44.97c0-0.22,0-0.4,0-0.57c0-6.63,0-13.25,0-19.88c0-0.36,0-0.72,0.06-1.07c0.39-2.05,2.03-3.42,4.12-3.43
		c3.41-0.01,6.82,0,10.23,0c0.17,0,0.34,0,0.5,0c0-3.37,0-6.67,0-9.99c-1.89,0-3.74,0.01-5.59,0c-1.65-0.01-2.67-1.04-2.67-2.67
		c0-1.02,0-2.05,0-3.07c0-0.84,0-1.68,0-2.57c-0.22,0-0.39,0-0.57,0c-7.47,0-14.94,0-22.41,0c-1.34,0-1.97,0.63-1.97,1.96
		c0,13.11,0,26.21,0,39.32c0,1.34,0.63,1.98,1.96,1.98c3.04,0,6.07,0,9.11,0C15.17,44.97,17.55,44.97,20.02,44.97z M35.04,21.68
		c-3.56,0-7.11,0-10.67,0c-1.68,0-2.69,1.02-2.69,2.7c0,7.07,0,14.13,0,21.2c0,1.68,1.02,2.71,2.69,2.71c7.07,0,14.13,0,21.2,0
		c1.69,0,2.72-1.04,2.72-2.74c0-7.05,0-14.1,0-21.15c0-1.71-1.02-2.72-2.72-2.72C42.06,21.68,38.55,21.68,35.04,21.68z M28.32,3.03
		c0,1.44,0,2.89,0,4.33c0,0.71,0.3,1,1.01,1c0.92,0.01,1.85,0,2.77,0c0.52,0,1.05,0,1.57,0C31.87,6.56,30.1,4.8,28.32,3.03z"/>
	<path class="st1" d="M18.32,13.36c4.32,0,8.64,0,12.95,0c0.82,0,1.23,0.29,1.22,0.85c-0.02,0.55-0.42,0.82-1.21,0.82
		c-8.62,0-17.24,0-25.86,0c-0.21,0-0.43,0-0.63-0.06c-0.41-0.12-0.6-0.42-0.57-0.85c0.03-0.41,0.26-0.66,0.66-0.73
		c0.19-0.04,0.39-0.03,0.58-0.03C9.75,13.36,14.03,13.36,18.32,13.36z"/>
	<path class="st2" d="M11.66,20.83c-2.08,0-4.16,0-6.24,0c-0.16,0-0.33,0.01-0.49-0.01c-0.43-0.06-0.67-0.3-0.71-0.73
		c-0.04-0.42,0.15-0.73,0.56-0.86c0.18-0.06,0.38-0.06,0.58-0.06c4.22,0,8.45,0,12.67,0c0.11,0,0.23-0.01,0.34,0.01
		c0.5,0.06,0.81,0.4,0.79,0.87c-0.02,0.45-0.33,0.76-0.82,0.78c-0.55,0.02-1.1,0.01-1.66,0.01C15.01,20.83,13.34,20.83,11.66,20.83z
		"/>
	<path class="st3" d="M11.31,36.64c1.95,0,3.89,0,5.84,0c0.19,0,0.39,0,0.58,0.05c0.4,0.09,0.61,0.39,0.62,0.77
		c0.01,0.41-0.2,0.71-0.63,0.81c-0.16,0.04-0.32,0.04-0.48,0.04c-3.97,0-7.95,0-11.92,0c-0.45,0-0.85-0.08-1.05-0.54
		c-0.24-0.57,0.18-1.12,0.86-1.14c0.79-0.02,1.59,0,2.38,0C8.78,36.64,10.04,36.64,11.31,36.64z"/>
	<path class="st4" d="M11.26,26.66c-2.03,0-4.06,0-6.09,0c-0.65,0-1.02-0.39-0.94-0.95c0.06-0.43,0.39-0.7,0.9-0.7
		C6.22,25,7.31,25,8.4,25c2.96,0,5.91,0,8.87,0.01c0.25,0,0.53,0.05,0.73,0.17c0.31,0.19,0.43,0.52,0.3,0.89
		c-0.13,0.38-0.4,0.58-0.8,0.58c-1.1,0.01-2.21,0-3.31,0C13.21,26.66,12.23,26.66,11.26,26.66z"/>
	<path class="st5" d="M11.31,30.83c2,0,3.99,0,5.99,0c0.66,0,1.05,0.31,1.05,0.82c0,0.51-0.39,0.84-1.04,0.84
		c-4.03,0-8.05,0-12.08,0c-0.66,0-1.02-0.32-1.01-0.85c0.01-0.52,0.35-0.81,1-0.81C7.25,30.83,9.28,30.83,11.31,30.83z"/>
	<path class="st6" d="M31.64,30.83c0-1.31,0-2.58,0-3.84c0-0.65-0.01-1.3,0-1.95c0.03-1.02,0.72-1.69,1.74-1.7
		c1.01-0.01,2.01,0,3.02,0c1.26,0,1.9,0.64,1.91,1.92c0,1.66,0,3.31,0,4.97c0,0.18,0,0.35,0,0.6c0.4,0,0.76,0,1.13,0
		c0.92,0,1.6,0.4,1.98,1.24c0.38,0.84,0.3,1.67-0.33,2.36c-1.54,1.67-3.09,3.33-4.68,4.96c-0.8,0.83-2.08,0.81-2.88-0.02
		c-1.56-1.61-3.09-3.24-4.61-4.89c-0.64-0.69-0.77-1.53-0.38-2.4c0.39-0.85,1.08-1.26,2.02-1.25
		C30.89,30.83,31.23,30.83,31.64,30.83z M33.31,25.02c0,2.18,0,4.31,0,6.43c0,0.75-0.29,1.03-1.02,1.04c-0.55,0-1.11-0.02-1.66,0.01
		c-0.2,0.01-0.49,0.12-0.54,0.26c-0.07,0.18-0.01,0.5,0.12,0.64c1.46,1.59,2.95,3.15,4.44,4.72c0.22,0.23,0.46,0.24,0.68,0.01
		c1.5-1.58,2.99-3.16,4.47-4.76c0.12-0.13,0.16-0.44,0.1-0.61c-0.05-0.13-0.32-0.25-0.5-0.26c-0.58-0.03-1.17-0.01-1.75-0.01
		c-0.67-0.01-0.98-0.31-0.99-0.99c-0.01-0.63,0-1.27,0-1.9c0-1.52,0-3.04,0-4.58C35.52,25.02,34.44,25.02,33.31,25.02z"/>
	<path class="st5" d="M34.99,41.65c1.69,0,3.38-0.02,5.07,0.01c1.08,0.02,1.85,0.58,2.24,1.58c0.38,0.98,0.17,1.89-0.55,2.65
		c-0.47,0.49-1.07,0.74-1.74,0.74c-3.34,0.01-6.69,0.02-10.03,0c-1.4-0.01-2.46-1.13-2.45-2.5c0-1.38,1.05-2.46,2.47-2.47
		C31.64,41.63,33.32,41.64,34.99,41.65C34.99,41.64,34.99,41.65,34.99,41.65z M34.99,43.31c-1.61,0-3.21,0-4.82,0
		c-0.64,0-0.99,0.28-1,0.81c-0.01,0.54,0.35,0.85,1.01,0.85c3.2,0,6.39,0,9.59,0c0.65,0,1.03-0.32,1.02-0.84
		c0-0.53-0.36-0.82-1.04-0.82C38.17,43.3,36.58,43.31,34.99,43.31z"/>
</g>
</svg>' . "<div>{$text}</div></a>";
        }
        $result .= '</div>';
        return $result;
    }
}