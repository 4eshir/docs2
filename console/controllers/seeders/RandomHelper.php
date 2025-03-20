<?php

namespace console\controllers\seeders;
use DateTimeImmutable;
use DateTimeInterface;
class RandomHelper
{
    public function randomDate(){
        $t1 = (new DateTimeImmutable('2022-01-01T00:00:00'))->getTimestamp();
        $t2 = (new DateTimeImmutable('2026-01-01T00:00:00'))->getTimestamp();
        $t = rand($t1, $t2);
        return (new DateTimeImmutable())->setTimestamp($t)->format(DateTimeInterface::RFC3339);
    }
    public function randomItem($array){
        return $array[array_rand($array)];
    }
    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
    public function randomString($length){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}