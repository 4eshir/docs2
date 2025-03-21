<?php

namespace common\components\wizards;

class WordWizard
{
    public static function convertMillimetersToTwips($millimeters)
    {
        return floor($millimeters * 56.7);
        // переход на новую строку в едином тексте "<w:br/>"
    }
}