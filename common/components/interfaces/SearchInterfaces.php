<?php

namespace common\components\interfaces;

interface SearchInterfaces {
    public function rules();

    public function sortAttributes($dataProvider);

    public function search($params);

}