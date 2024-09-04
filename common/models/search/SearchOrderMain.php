<?php

namespace common\models\search;

use common\helpers\DateFormatter;
use common\models\work\document_in_out\DocumentInWork;
use OrderMainWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class SearchOrderMain extends OrderMainWork
{
    public $fullNumber;
    public $Date;
    public function rules(){
        return [
            [['id', 'order_copy_id', 'position_id', 'bring_id', 'signed_id', 'executor_id', 'creator_id'], 'integer'],
            [['realNumber', 'fullNumber'], 'string'],
            [['Date', 'realDate', 'documentTheme', 'correspondentName', 'companyName', 'sendMethodName'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {

    }
}