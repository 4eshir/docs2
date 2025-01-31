<?php

namespace common\helpers\html;

use common\helpers\common\BaseFunctions;
use common\helpers\files\FilePaths;
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
     * $linksArray должен быть ассоциативным массивом ['Имя кнопки' => ['url' => ['ссылка'], 'class' => '...', 'data' => [...] ], ...]
     * параметры class и data являются не обязательными
     * @param $linksArray
     * @return string
     */
    public static function createGroupButton($linksArray)
    {
        $result = '<div class="button-group">';

        foreach ($linksArray as $label => $linkOptions) {
            $url = $linkOptions['url'];
            $class = $linkOptions['class'] ?? 'btn-secondary'; // Класс по умолчанию
            $data = $linkOptions['data'] ?? [];

            $result .= Html::a($label, $url, ['class' => [$class], 'data' => $data,]);
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

    public static function createSVGLink($link)
    {
        preg_match('/href="([^"]+)"/', $link, $filePath);
        $svgFile = FilePaths::FILE_DOWNLOAD_SVG;
        if(empty($filePath[1]))
        {
            $svgFile = FilePaths::FILE_NO_DOWNLOAD_SVG;
            $filePath = '#';
        }
        $svgContent = file_get_contents($svgFile);


        $result = '<div class="fileIcon">';
        $result .= '<a href="' . $filePath[1] .'" class="download">' . $svgContent . '</a>';
        $result .= '</div>';
        return $result;
    }
}