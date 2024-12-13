<?php

namespace frontend\models\search;

use common\components\interfaces\SearchInterfaces;
use frontend\models\search\abstractBase\DocumentSearch;
use frontend\models\work\document_in_out\DocumentInWork;
use yii\data\ActiveDataProvider;

/**
 * SearchDocumentIn represents the model behind the search form of `app\models\common\DocumentIn`.
 */
class SearchDocumentIn extends DocumentSearch implements SearchInterfaces
{
    public $correspondentName;
    public $number;


    public function rules()
    {
        parent::rules();
        return [
            [['local_number'], 'integer'],
            [['realNumber'], 'string'],
            [['localDate', 'realDate', 'correspondentName', 'number'], 'safe'],
        ];
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
            ->joinWith(['company','correspondent','correspondent.people', 'inOutDocument.responsible.people']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['local_date' => SORT_DESC, 'local_number' => SORT_DESC, 'local_postfix' => SORT_DESC]]
        ]);

        $this->sortAttributes($dataProvider);
        $this->filterStatus($query);
        $this->filterDate($query);
        $this->filterNumber($query);
        $this->filterCorrespondentName($query);
        $this->filterTheme($query);
        $this->filterSendMethodName($query);
        $this->filterExecutorName($query);
        $this->filterKeyWords($query);

        return $dataProvider;
    }

    public function sortAttributes($dataProvider) {
        parent::sortAttributes($dataProvider);

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

        $dataProvider->sort->attributes['needAnswer'] = [
            'asc' => ['need_answer' => SORT_DESC],
            'desc' => ['need_answer' => SORT_ASC],
        ];
    }

    /**
     * @param $query
     * @return void
     */
    private function filterStatus($query) {
        $statusConditions = [
            '1' => [],
            '2' => ['<', 'date', date('Y-m-d')],
            '3' => ['=', 'need_answer', 1],
            '4' => ['>', 'local_date', date('Y') . '-01-01'],
        ];
        $query->andWhere($statusConditions[$this->status] ?? ['>', 'local_date', date('Y') . '-01-01']);
    }

    private function filterCorrespondentName($query) {
        $query->andFilterWhere(['or',
                ['like', 'LOWER(company.name)', mb_strtolower($this->correspondentName)],
                ['like', 'LOWER(company.short_name)', mb_strtolower($this->correspondentName)],
                ['like', 'LOWER(people.firstname)', mb_strtolower($this->correspondentName)],
            ]);
    }

    private function filterDate($query) {
        if ($this->startDateSearch != '' && $this->finishDateSearch != '')
        {
            $dateFrom = date('Y-m-d', strtotime($this->startDateSearch));
            $dateTo =  date('Y-m-d', strtotime($this->finishDateSearch));
            $query->andWhere(['or',
                ['between', 'local_date', $dateFrom, $dateTo],
                ['between', 'real_date', $dateFrom, $dateTo],
            ]);
        }
    }

    private function filterNumber($query) {
        $query->andFilterWhere(['or',
            ['like', 'real_number', $this->number],
            ['like', "CONCAT(local_number, '/', local_postfix)", $this->number],
        ]);
    }
}
