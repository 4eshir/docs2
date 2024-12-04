<?php

namespace frontend\models\search;

use DateTime;
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
    public $documentTheme;

    public $correspondentName;
    public $startDateSearch;
    public $finishDateSearch;
    public $number;
    public $executorName;
    public $status;

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
            [['id', 'local_number', 'position_id', 'company_id', 'signed_id', 'get_id', 'creator_id'], 'integer'],
            [['realNumber', 'fullNumber', 'key_words'], 'string'],
            [['startDateSearch', 'finishDateSearch'], 'date', 'format' => 'dd.MM.yyyy'],
            [['localDate', 'realDate', 'documentTheme', 'correspondentName', 'companyName', 'sendMethodName', 'number'], 'safe'],
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
            ->joinWith('company')
            ->joinWith('correspondent')->joinWith('correspondent.people');

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

        if ($this->startDateSearch != '' && $this->finishDateSearch != '')
        {
            $dateFrom = date('Y-m-d', strtotime($this->startDateSearch));
            $dateTo =  date('Y-m-d', strtotime($this->finishDateSearch));
            $query->andWhere(['or',
                ['between', 'local_date', $dateFrom, $dateTo],
                ['between', 'real_date', $dateFrom, $dateTo],
            ]);
        }

        // гибкие фильтры Like
        $query->andFilterWhere(['or',
                ['like', 'real_number', $this->number],
                ['like', "CONCAT(local_number, '/', local_postfix)", $this->number],
            ])
            ->andFilterWhere(['like', 'document_theme', $this->documentTheme])
            ->andFilterWhere(['like', 'LOWER(key_words)', mb_strtolower($this->key_words)])
            ->andFilterWhere(['or',
                ['like', 'LOWER(company.name)', mb_strtolower($this->correspondentName)],
                ['like', 'LOWER(company.short_name)', mb_strtolower($this->correspondentName)],
                ['like', 'LOWER(people.firstname)', mb_strtolower($this->correspondentName)],
            ]);
            /*->andFilterWhere(['like', ])    // исполнитель
            ->andFilterWhere(['like', ])    // способ получения
            ->andFilterWhere(['like', ])    // статус документа*/

        return $dataProvider;
    }
}
