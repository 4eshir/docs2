<?php

namespace frontend\models\search;

use common\components\interfaces\SearchInterfaces;
use common\helpers\DateFormatter;
use frontend\models\work\event\EventWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * SearchEvent represents the model behind the search form of `app\models\common\Event`.
 */
class SearchEvent extends Model implements SearchInterfaces
{
    public string $startDateSearch;     // дата начала поиска в диапазоне дат
    public string $finishDateSearch;    // дата окончания поиска в диапазоне дат
    public string $eventName;           // наименование мероприятия или его части
    public int $eventWay;               // формат проведения
    public int $eventLevel;             // уровень мероприятия
    public int $eventType;              // тип мероприятия
    


    public function rules()
    {
        return [
            [['id', 'eventWay', 'eventLevel', 'eventType'], 'integer'],
            [['fullNumber'], 'string'],
            [['startDateSearch', 'finishDateSearch'], 'date', 'format' => 'dd.MM.yyyy'],
            [['datePeriod', 'startDateSearch', 'finishDateSearch', 'eventName', 'eventWay', 'eventLevel', 'eventType'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function __construct(
        string $startDateSearch = '',
        string $finishDateSearch = '',
        int $eventLevel = -1,
        string $eventName = '',
        int $eventType = -1,
        int $eventWay = -1,
        $config = []
    ) {
        parent::__construct($config);
        $this->startDateSearch = $startDateSearch;
        $this->finishDateSearch = $finishDateSearch;
        $this->eventLevel = $eventLevel;
        $this->eventName = $eventName;
        $this->eventWay = $eventWay;
        $this->eventType = $eventType;
    }

    public function search($params)
    {
        $params['SearchEvent']['eventWay'] = empty($params['SearchEvent']['eventWay']) ? -1 : (int) $params['SearchEvent']['eventWay'];
        $params['SearchEvent']['eventType'] = empty($params['SearchEvent']['eventType']) ? -1 : (int) $params['SearchEvent']['eventType'];
        $params['SearchEvent']['eventLevel'] = empty($params['SearchEvent']['eventLevel']) ? -1 : (int) $params['SearchEvent']['eventLevel'];

        $this->load($params);

        $query = EventWork::find()
                ->joinWith([
                    'documentOrder' => function ($query) {
                        $query->alias('orderMain');
                    },
                ])
                ->joinWith([
                    'regulation'
                ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->sortAttributes($dataProvider);
        $this->filterQueryParams($query);

        return $dataProvider;
    }

    public function sortAttributes(ActiveDataProvider $dataProvider)
    {
        $dataProvider->sort->attributes['eventName'] = [
            'asc' => ['name' => SORT_ASC],
            'desc' => ['name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['datePeriod'] = [
            'asc' => ['start_date' => SORT_ASC, 'finish_date' => SORT_ASC],
            'desc' => ['start_date' => SORT_DESC, 'finish_date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['eventLevelAndType'] = [
            'asc' => ['event_level' => SORT_ASC, 'event_type' => SORT_ASC],
            'desc' => ['event_level' => SORT_DESC, 'event_type' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['address'] = [
            'asc' => ['address' => SORT_ASC],
            'desc' => ['address' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['participantCount'] = [
            'asc' => ['participant_count' => SORT_ASC],
            'desc' => ['participant_count' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['orderName'] = [
            'asc' => ['orderMain.order_name' => SORT_ASC],
            'desc' => ['orderMain.order_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['eventWay'] = [
            'asc' => ['start_date' => SORT_ASC],
            'desc' => ['start_date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['regulationRaw'] = [
            'asc' => ['regulation.name' => SORT_ASC],
            'desc' => ['regulation.name' => SORT_DESC],
        ];
    }

    public function filterQueryParams(ActiveQuery $query) {
        $this->filterDate($query);
        $this->filterName($query);
        $this->filterWay($query);
        $this->filterType($query);
        $this->filterLevel($query);
    }

    /**
     * Фильтрация мероприятий по диапазону дат
     *
     * @param ActiveQuery $query
     * @return void
     */
    public function filterDate(ActiveQuery $query) {
        if (!empty($this->startDateSearch) || !empty($this->finishDateSearch))
        {
            $dateFrom = $this->startDateSearch ? date('Y-m-d', strtotime($this->startDateSearch)) : DateFormatter::DEFAULT_YEAR_START;
            $dateTo =  $this->finishDateSearch ? date('Y-m-d', strtotime($this->finishDateSearch)) : date('Y-m-d');

            $query->andWhere(['or',
                ['between', 'start_date', $dateFrom, $dateTo],
                ['between', 'finish_date', $dateFrom, $dateTo],
            ]);
        }
    }

    /**
     * Фильтрация мероприятий по названию
     *
     * @param ActiveQuery $query
     * @return void
     */
    public function filterName(ActiveQuery $query) {
        if (!empty($this->eventName)) {
            $query->andWhere(['like', 'LOWER(event.name)', mb_strtolower($this->eventName)]);
        }
    }

    /**
     * Фильтрация мероприятий по формату проведения
     *
     * @param ActiveQuery $query
     * @return void
     */
    public function filterWay(ActiveQuery $query) {
        if ($this->eventWay !== -1) {
            $query->andFilterWhere(['event_way' => $this->eventWay]);
        }
    }

    /**
     * Фильтрация мероприятий по типу мероприятия
     *
     * @param ActiveQuery $query
     * @return void
     */
    public function filterType(ActiveQuery $query) {
        if ($this->eventType !== -1) {
            $query->andFilterWhere(['event_type' => $this->eventType]);
        }
    }

    /**
     * Фильтрация мероприятий по типу мероприятия
     *
     * @param ActiveQuery $query
     * @return void
     */
    public function filterLevel(ActiveQuery $query) {
        if ($this->eventLevel !== -1) {
            $query->andFilterWhere(['event_level' => $this->eventLevel]);
        }
    }
}
