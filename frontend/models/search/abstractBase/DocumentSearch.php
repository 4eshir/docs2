<?php

namespace frontend\models\search\abstractBase;

use yii\base\Model;

class DocumentSearch extends Model
{
    public $fullNumber;
    public $companyName;
    public $sendMethodName;
    public $documentTheme;
    public $startDateSearch;
    public $finishDateSearch;
    public $executorName;
    public $status;
    public $keyWords;

    public function rules()
    {
        return [
            [['id', 'positionId', 'companyId', 'signedId', 'getId', 'creatorId'], 'integer'],
            [['fullNumber', 'keyWords'], 'string'],
            [['startDateSearch', 'finishDateSearch'], 'date', 'format' => 'dd.MM.yyyy'],
            [['documentTheme', 'companyName', 'sendMethodName', 'executorName', 'status'], 'safe'],
        ];
    }

    public function __construct(
        string $fullNumber,
        string $companyName,
        int $sendMethodName,
        string $documentTheme,
        string $startDateSearch,
        string $finishDateSearch,
        string $executorName,
        int $status,
        string $keyWords
    ) {
        parent::__construct();
        $this->fullNumber = $fullNumber;
        $this->companyName = $companyName;
        $this->sendMethodName = $sendMethodName;
        $this->documentTheme = $documentTheme;
        $this->startDateSearch = $startDateSearch;
        $this->finishDateSearch = $finishDateSearch;
        $this->executorName = $executorName;
        $this->status = $status;
        $this->keyWords = $keyWords;
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function sortAttributes($dataProvider) {
        $dataProvider->sort->attributes['fullNumber'] = [
            'asc' => ['local_number' => SORT_ASC, 'local_postfix' => SORT_ASC],
            'desc' => ['local_number' => SORT_DESC, 'local_postfix' => SORT_DESC],
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
    }

    public function filterTheme($query) {
        $query->andFilterWhere(['like', 'document_theme', $this->documentTheme]);
    }

    public function filterKeyWords($query) {
        $query->andFilterWhere(['like', 'LOWER(key_words)', mb_strtolower($this->keyWords)]);
    }

    public function filterExecutorName($query) {
        $query->andFilterWhere(['like', 'LOWER(people.firstname)', mb_strtolower($this->executorName)]);  // исполнитель
    }

    public function filterSendMethodName($query) {
        $query->andFilterWhere(['like', 'send_method', $this->sendMethodName]);
    }
}