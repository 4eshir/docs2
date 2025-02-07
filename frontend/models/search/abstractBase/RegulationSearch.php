<?php

namespace frontend\models\search\abstractBase;

use common\helpers\DateFormatter;
use frontend\models\work\regulation\RegulationWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class RegulationSearch extends Model
{
    public $startDateSearch;    // стартовая дата поиска положений
    public $finishDateSearch;   // конечная дата поиска положений
    public $nameRegulation;     // краткое или полное наименование положения
    public $orderName;              // добавленный к положению приказ
    public $status;             // статус положения

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['nameRegulation', 'orderName'], 'string'],
            [['startDateSearch', 'finishDateSearch'], 'date', 'format' => 'dd.MM.yyyy'],
            [['startDateSearch', 'finishDateSearch', 'status'], 'safe'],
        ];
    }

    public function __construct(
        string $startDateSearch = null,
        string $finishDateSearch = null,
        string $nameRegulation = null,
        string $orderName = null,
        int $status = null
    ) {
        parent::__construct();
        $this->startDateSearch = $startDateSearch;
        $this->finishDateSearch = $finishDateSearch;
        $this->nameRegulation = $nameRegulation;
        $this->orderName = $orderName;
        $this->status = $status;
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Сортировка атрибутов запроса
     *
     * @param $dataProvider
     * @return void
     */
    public function sortAttributes(ActiveDataProvider $dataProvider) {
        $dataProvider->sort->attributes['date'] = [
            'asc' => ['date' => SORT_ASC],
            'desc' => ['date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['nameRegulation'] = [
            'asc' => ['name' => SORT_ASC],
            'desc' => ['name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['orderName'] = [
            'asc' => ['orderMain.order_name' => SORT_ASC],
            'desc' => ['orderMain.order_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['state'] = [
            'asc' => ['state' => SORT_ASC],
            'desc' => ['state' => SORT_DESC],
        ];
    }

    /**
     * Вызов функций фильтров по параметрам запроса
     *
     * @param ActiveQuery $query
     * @return void
     */
    public function filterAbstractQueryParams(ActiveQuery $query) {
        $this->filterDate($query);
        $this->filterName($query);
        $this->filterOrderName($query);
        $this->filterStatus($query);
    }

    /**
     * Фильтрация документов по диапазону дат
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterDate(ActiveQuery $query) {
        if ($this->startDateSearch != '' || $this->finishDateSearch != '')
        {
            $dateFrom = $this->startDateSearch ? date('Y-m-d', strtotime($this->startDateSearch)) : DateFormatter::DEFAULT_YEAR_START;
            $dateTo =  $this->finishDateSearch ? date('Y-m-d', strtotime($this->finishDateSearch)) : date('Y-m-d');

            $query->andWhere(['between', 'date', $dateFrom, $dateTo]);
        }
    }

    /**
     * Фильтрация положений по наименованию или крткому наименованию
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterName(ActiveQuery $query) {
        $query->andFilterWhere(['or',
            ['like', 'LOWER(name)', mb_strtolower($this->nameRegulation)],
            ['like', 'LOWER(short_name)', mb_strtolower($this->nameRegulation)],
        ]);
    }

    /**
     * Фильтрация положений по наименованию приказа
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterOrderName(ActiveQuery $query) {
        $query->andFilterWhere(['or',
            ['like', 'LOWER(order_name)', mb_strtolower($this->orderName)],
            ['like', "CONCAT(order_number, '/', order_postfix)", $this->orderName],
        ]);
    }

    /**
     * Фильтрация статуса: актуально/утратило силу
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterStatus(ActiveQuery $query) {
        $query->andFilterWhere(['like', 'regulation.state', $this->status]);
    }
}