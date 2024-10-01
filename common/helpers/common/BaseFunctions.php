<?php

namespace common\helpers\common;

class BaseFunctions
{
    public static function transposeMatrix(array $matrix): array
    {
        $transposed = [];

        foreach ($matrix as $row) {
            foreach ($row as $key => $value) {
                $transposed[$key][] = $value;
            }
        }

        return $transposed;
    }
}