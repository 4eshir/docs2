<?php

namespace controllers;

use common\models\LoginForm;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

/**
 * Site controller
 */
class UserController extends Controller
{
    public function actionCreate()
    {
        $splitFio = [];

        while (count($splitFio) < 2) {
            $fio = $this->prompt('Введите ФИО пользователя через пробел: ');
            $splitFio = explode(' ', $fio);
        }

        $login = $this->prompt('Введите логин пользователя: ');
        $password = $this->prompt('Введите пароль пользователя: ');

        $password = Yii::$app->security->generatePasswordHash($password);


    }
}
