<?php

namespace common\models\search;

use common\components\traits\DataFormatTrait;
use common\models\work\document_in_out\DocumentInWork;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SearchDocumentIn represents the model behind the search form of `app\models\common\DocumentIn`.
 */
class SearchDocumentIn extends DocumentInWork
{
    use DataFormatTrait;

    public $fullNumber;
    public $companyName;
    public $sendMethodName;
    public $localDate;
    public $realDate;
    public $realNumber;
    public $documentTheme;

    public $archive;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'local_number', 'position_id', 'company_id', 'signed_id', 'get_id', 'creator_id', 'archive'], 'integer'],
            [['realNumber', 'fullNumber'], 'string'],
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
            $dates = $this->splitDates($this->localDate);
            $query->andWhere(['BETWEEN', 'local_date', $this->format($dates[0], self::$dmy_dot, self::$Ymd_dash), $this->format($dates[1], self::$dmy_dot, self::$Ymd_dash)]);
        }

        if ($this->realDate !== '' && $this->realDate !== null) {
            $dates = $this->splitDates($this->realDate);
            $query->andWhere(['BETWEEN', 'real_date', $dates[0], $dates[1]]);
        }

        //var_dump($query->createCommand()->getRawSql());

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
            /*'documentTheme' => $this->document_theme,
            'localNumber' => $this->local_number,
            'localDate' => $this->local_date,
            'realNumber' => $this->real_number,
            'realDate' => $this->real_date,
            'position_id' => $this->position_id,
            'company_id' => $this->company_id,
            'correspondent_id' => $this->correspondent_id,
            'signed_id' => $this->signed_id,
            'get_id' => $this->get_id,
            'creator_id' => $this->creator_id,*/
        ]);

        // гибкие фильтры Like
        $query
            ->andFilterWhere(['like', 'document_theme', $this->documentTheme]);

        return $dataProvider;
    }
}
