<?php

namespace frontend\models\search;

use frontend\models\work\educational\training_group\TrainingGroupWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SearchTrainingGroup represents the model behind the search form of `app\models\common\TrainingGroup`.
 */
class SearchTrainingGroup extends TrainingGroupWork
{
    public $programName;
    public $numberView;
    public $budgetText;
    public $branchId;
    public $teacherId;
    public $startDateSearch;
    public $finishDateSearch;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'number', 'training_program_id', 'teacher_id', 'open', 'budgetText'], 'integer'],
            [['start_date', 'finish_date', 'photos', 'present_data', 'work_data', 'branchId', 'teacherId', 'startDateSearch', 'finishDateSearch'], 'safe'],
            [['programName', 'numberView'], 'string'],
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
        /*if (array_key_exists("SearchTrainingGroup", $params))
        {
            if ($params["SearchTrainingGroup"]["branchId"] !== null && $params["SearchTrainingGroup"]["branchId"] !== "")
            {
                $groups = $groups->andWhere(['IN', 'training_group.id', (new Query())->select('id')->from('training_group')->where(['branch_id' => $params ["SearchTrainingGroup"]["branchId"]])]);

            }
            if ($params["SearchTrainingGroup"]["teacherId"] !== null && $params["SearchTrainingGroup"]["teacherId"] !== "")
            {
                $tg = TeacherGroupWork::find()->where(['teacher_id' => $params["SearchTrainingGroup"]["teacherId"]])->all();
                $tgIds = [];
                foreach ($tg as $oneTg) $tgIds[] = $oneTg->training_group_id;
                $groups = $groups->andWhere(['IN', 'training_group.id', (new Query())->select('id')->from('training_group')->where(['IN', 'training_group.id', $tgIds])]);
            }
            if ($params["SearchTrainingGroup"]["startDateSearch"] !== null && $params["SearchTrainingGroup"]["finishDateSearch"] !== null &&
                $params["SearchTrainingGroup"]["startDateSearch"] !== "" && $params["SearchTrainingGroup"]["finishDateSearch"] !== "")
            {
                $groups = $groups->andWhere(['IN', 'training_group.id', (new Query())->select('id')->from('training_group')
                    ->where(['<=', 'start_date', $params["SearchTrainingGroup"]["finishDateSearch"]])->andWhere(['>=', 'finish_date', $params["SearchTrainingGroup"]["startDateSearch"]])
                    ->orWhere(['>=', 'finish_date', $params["SearchTrainingGroup"]["startDateSearch"]])->andWhere(['<=', 'start_date', $params["SearchTrainingGroup"]["finishDateSearch"]])]);

            }
        }*/


        $query = TrainingGroupWork::find();
        //$query = $groups;
        $query->joinWith(['trainingProgram trainingProgram']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        /*$dataProvider->sort->attributes['programName'] = [
            'asc' => ['trainingProgram.name' => SORT_ASC],
            'desc' => ['trainingProgram.name' => SORT_DESC],
        ];


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'number' => $this->number,
            'training_program_id' => $this->training_program_id,
            'teacher_id' => $this->teacher_id,
            'start_date' => $this->start_date,
            'finish_date' => $this->finish_date,
            'open' => $this->open,
            'budget' => $this->budget,
        ]);

        $query->andFilterWhere(['like', 'photos', $this->photos])
            ->andFilterWhere(['like', 'present_data', $this->present_data])
            ->andFilterWhere(['like', 'number', $this->numberView])
            ->andFilterWhere(['like', 'budget', $this->budgetText])
            ->andFilterWhere(['like', 'trainingProgram.name', $this->programName])
            ->andFilterWhere(['like', 'work_data', $this->work_data]);*/

        return $dataProvider;
    }
}
