<?php

namespace frontend\models\search;

use common\helpers\DateFormatter;
use frontend\models\work\document_in_out\DocumentInWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SearchDocumentIn represents the model behind the search form of `app\models\common\DocumentIn`.
 */
class SearchDocumentIn extends DocumentInWork
{
    public $fullNumber;
    public $companyName;
    public $sendMethodName;
    public $localDate;
    public $realDate;
    public $realNumber;
    public $documentTheme;

    public $archive;

    public $correspondentName;
    public $start_date_search;
    public $finish_date_search;

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'correspondentName' => 'Корреспондент',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'local_number', 'position_id', 'company_id', 'signed_id', 'get_id', 'creator_id', 'archive'], 'integer'],
            [['realNumber', 'fullNumber', 'key_words'], 'string'],
            [['localDate', 'realDate', 'documentTheme', 'correspondentName', 'companyName', 'sendMethodName'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
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
        $this->load($params);
        $query = DocumentInWork::find()
            ->joinWith('company');

        if ($this->localDate !== '' && $this->localDate !== null) {
            $dates = DateFormatter::splitDates($this->localDate);
            $query->andWhere(
                ['BETWEEN', 'local_date',
                    DateFormatter::format($dates[0], DateFormatter::dmy_dot, DateFormatter::Ymd_dash),
                    DateFormatter::format($dates[1], DateFormatter::dmy_dot, DateFormatter::Ymd_dash)]);
        }

        if ($this->realDate !== '' && $this->realDate !== null) {
            $dates = DateFormatter::splitDates($this->realDate);
            $query->andWhere(['BETWEEN', 'real_date', $dates[0], $dates[1]]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['local_date' => SORT_DESC, 'local_number' => SORT_DESC, 'local_postfix' => SORT_DESC]]
        ]);

        $dataProvider->sort->attributes['fullNumber'] = [
            'asc' => ['local_number' => SORT_ASC, 'local_postfix' => SORT_ASC],
            'desc' => ['local_number' => SORT_DESC, 'local_postfix' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['localDate'] = [
            'asc' => ['local_date' => SORT_ASC],
            'desc' => ['local_date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['realDate'] = [
            'asc' => ['real_date' => SORT_ASC],
            'desc' => ['real_date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['realNumber'] = [
            'asc' => ['real_number' => SORT_ASC],
            'desc' => ['real_number' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['companyName'] = [
            'asc' => ['company.name' => SORT_ASC],
            'desc' => ['company.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['documentTheme'] = [
            'asc' => ['document_theme' => SORT_ASC],
            'desc' => ['document_theme' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['sendMethodName'] = [
            'asc' => ['send_method' => SORT_ASC],
            'desc' => ['send_method' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['needAnswer'] = [
            'asc' => ['need_answer' => SORT_DESC],
            'desc' => ['need_answer' => SORT_ASC],
        ];

        //var_dump($this->realDate);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // строгие фильтры
        $query->andFilterWhere([
            'send_method' => $this->sendMethodName,
        ]);

        // гибкие фильтры Like
        $query->andFilterWhere(['like', "CONCAT(local_number, '/', local_postfix)", $this->fullNumber])
            ->andFilterWhere(['like', 'real_number', $this->realNumber])
            ->andFilterWhere(['like', 'company.name', $this->companyName])
            ->andFilterWhere(['like', 'document_theme', $this->documentTheme])
            ->andFilterWhere(['like', 'real_number', $this->realNumber]);
        return $dataProvider;
    }
}
