<?php

namespace common\helpers\html;

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
}