<?php

namespace frontend\models\search;

use common\components\interfaces\SearchInterfaces;
use frontend\models\work\event\EventWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * SearchEvent represents the model behind the search form of `app\models\common\Event`.
 */
class SearchEvent extends Model implements SearchInterfaces
{
    public $startDateSearch;
    public $finishDateSearch;
    public $eventName;

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['fullNumber'], 'string'],
            [['startDateSearch', 'finishDateSearch'], 'date', 'format' => 'dd.MM.yyyy'],
            [['datePeriod'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
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

        /*if (array_key_exists("SearchEvent", $params))
        {
            if ($params["SearchEvent"]["eventBranchs"] != null)
            {
                $ebs = EventBranchWork::find()->where(['branch_id' => $params["SearchEvent"]["eventBranchs"]])->all();
                $eIds = [];
                foreach ($ebs as $eb) $eIds[] = $eb->event_id;
                $query = EventWork::find()->where(['IN', 'event.id', $eIds]);
            }

            if (strlen($params["SearchEvent"]["start_date_search"]) > 9 && strlen($params["SearchEvent"]["finish_date_search"]) > 9)
            {
                $query = $query->andWhere(['IN', 'event.id',
                    (new Query())->select('event.id')->from('event')->where(['>=', 'start_date', $params["SearchEvent"]["start_date_search"]])
                        ->andWhere(['<=', 'finish_date', $params["SearchEvent"]["finish_date_search"]])]);

            }
        }



        //SELECT * FROM `event` WHERE `id` IN (SELECT `event_id` FROM `event_branch` WHERE `branch_id` = 2)

        // add conditions that should always apply here

        $query->joinWith(['responsible responsible']);
        $query->joinWith(['eventLevel eventLevel']);
        $query->joinWith(['order order']);
        $query->joinWith(['regulation regulation']);*/

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->sortAttributes($dataProvider);

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

        $dataProvider->sort->attributes['eventType'] = [
            'asc' => ['event_type' => SORT_ASC],
            'desc' => ['event_type' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['address'] = [
            'asc' => ['address' => SORT_ASC],
            'desc' => ['address' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['eventLevel'] = [
            'asc' => ['event_level' => SORT_ASC],
            'desc' => ['event_level' => SORT_DESC],
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

    }
}
