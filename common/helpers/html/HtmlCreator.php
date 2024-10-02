<?php

namespace common\helpers\html;

class HtmlCreator
{
    public static function filterToggle() {
        return '<div class="filter-toggle" id="filterToggle">
                    <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path d="M9 12L4 4H15M20 4L15 12V21L9 18V16" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>';
    }
}