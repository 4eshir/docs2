<?php

namespace frontend\models\search;

use common\components\dictionaries\base\RegulationTypeDictionary;
use frontend\models\work\regulation\RegulationWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;


class SearchRegulationEvent extends SearchRegulation
{
    public function rules()
    {
        return parent::rules();
    }

    public function search($params)
    {
        $this->load($params);
        $query = RegulationWork::find()
                ->joinWith([
                    'documentOrder'/*=> function ($query) {
                        $query->alias('orderMain');
                    }*/,
                ])
                ->where(['regulation_type' => RegulationTypeDictionary::TYPE_EVENT]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        return $dataProvider;
    }
}
