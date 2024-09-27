<?php

namespace frontend\models\search;

use frontend\models\work\event\EventWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SearchEvent represents the model behind the search form of `app\models\common\Event`.
 */
class SearchEvent extends EventWork
{
    public $eventBranchs;

    public $responsibleString;
    public $eventLevelString;
    public $orderString;
    public $regulationString;

    public $start_date_search;
    public $finish_date_search;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [];
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
        $query = EventWork::find();

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

        /*$dataProvider->sort->attributes['responsibleString'] = [
            'asc' => ['responsible.short_name' => SORT_ASC],
            'desc' => ['responsible.short_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['eventLevelString'] = [
            'asc' => ['eventLevel.Name' => SORT_ASC],
            'desc' => ['eventLevel.Name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['orderString'] = [
            'asc' => ['order.order_name' => SORT_ASC],
            'desc' => ['order.order_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['regulationString'] = [
            'asc' => ['regulation.name' => SORT_ASC],
            'desc' => ['regulation.name' => SORT_DESC],
        ];*/

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        /*// grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'start_date' => $this->start_date,
            'finish_date' => $this->finish_date,
            'event_type_id' => $this->event_type_id,
            'event_form_id' => $this->event_form_id,
            'event_level_id' => $this->event_level_id,
            'participants_count' => $this->participants_count,
            'is_federal' => $this->is_federal,
            'responsible_id' => $this->responsible_id,
            'event.order_id' => $this->order_id,
            'regulation_id' => $this->regulation_id,
        ]);

        $query->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'event.name', $this->name])
            ->andFilterWhere(['like', 'event.key_words', $this->key_words])
            ->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'protocol', $this->protocol])
            ->andFilterWhere(['like', 'photos', $this->photos])
            ->andFilterWhere(['like', 'responsible.Secondname', $this->responsibleString])
            ->andFilterWhere(['like', 'eventLevel.Name', $this->eventLevelString])
            ->andFilterWhere(['like', 'order.order_name', $this->orderString])
            ->andFilterWhere(['like', 'regulation.name', $this->regulationString])
            ->andFilterWhere(['like', 'reporting_doc', $this->reporting_doc])
            ->andFilterWhere(['like', 'other_files', $this->other_files]);*/

        return $dataProvider;
    }
}
