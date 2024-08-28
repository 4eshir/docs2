<?php

namespace common\models\search;

use common\helpers\DateFormatter;
use common\models\work\document_in_out\DocumentOutWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class SearchDocumentOut extends DocumentOutWork
{
    public $fullNumber;
    public $companyName;
    public $sendMethodName;
    public $documentDate;
    public $sentDate;
    public $documentNumber;
    public $documentTheme;

    public $archive;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'document_number', 'position_id', 'company_id', 'signed_id', 'creator_id', 'archive', 'documentNumber'], 'integer'],
            [['fullNumber', 'documentNumber'], 'string'],
            [['documentDate', 'sentDate', 'documentTheme', 'correspondentName', 'companyName', 'sendMethodName'], 'safe'],
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

        $query = DocumentOutWork::find()
            ->joinWith('company');

        if ($this->documentDate !== '' && $this->documentDate !== null) {
            $dates = DateFormatter::splitDates($this->documentDate);
            $query->andWhere(
                ['BETWEEN', 'document_date',
                    DateFormatter::format($dates[0], DateFormatter::dmy_dot, DateFormatter::Ymd_dash),
                    DateFormatter::format($dates[1], DateFormatter::dmy_dot, DateFormatter::Ymd_dash)]);
        }

        if ($this->sentDate !== '' && $this->sentDate !== null) {
            $dates = DateFormatter::splitDates($this->sentDate);
            $query->andWhere(['BETWEEN', 'sent_date', $dates[0], $dates[1]]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['document_date' => SORT_DESC, 'document_number' => SORT_DESC, 'document_postfix' => SORT_DESC]]
        ]);
        $dataProvider->sort->attributes['fullNumber'] = [
            'asc' => ['document_number' => SORT_ASC, 'document_postfix' => SORT_ASC],
            'desc' => ['document_number' => SORT_DESC, 'document_postfix' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['documentDate'] = [
            'asc' => ['document_date' => SORT_ASC],
            'desc' => ['document_date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['sentDate'] = [
            'asc' => ['sent_date' => SORT_ASC],
            'desc' => ['sent_date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['documentNumber'] = [
            'asc' => ['document_number' => SORT_ASC],
            'desc' => ['document_number' => SORT_DESC],
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

        $dataProvider->sort->attributes['isAnswer'] = [
            'asc' => ['is_answer' => SORT_DESC],
            'desc' => ['is_answer' => SORT_ASC],
        ];


        if (!$this->validate()) {
            return $dataProvider;
        }

        // строгие фильтры
        $query->andFilterWhere([
            'send_method' => $this->sendMethodName,
        ]);

        // гибкие фильтры Like
        $query->andFilterWhere(['like', "CONCAT(document_number, '/', document_postfix)", $this->fullNumber])
            ->andFilterWhere(['like', 'document_number', $this->documentNumber])
            ->andFilterWhere(['like', 'company.name', $this->companyName])
            ->andFilterWhere(['like', 'document_theme', $this->documentTheme])
            ->andFilterWhere(['like', 'document_number', $this->documentNumber]);

        return $dataProvider;
    }
}