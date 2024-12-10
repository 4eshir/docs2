<?php

namespace common\components\compare;

abstract class AbstractCompare
{
    abstract public static function compare($c1, $c2) : bool;
}