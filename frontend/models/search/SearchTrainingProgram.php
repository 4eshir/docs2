<?php

namespace frontend\models\search;

use frontend\models\work\educational\training_program\TrainingProgramWork;
use yii\data\ActiveDataProvider;

/**
 * SearchTrainingProgram represents the model behind the search form of `app\models\common\TrainingProgram`.
 */
class SearchTrainingProgram extends TrainingProgramWork
{
    public $authorSearch;
    public $branchSearch;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'ped_council_date', 'author_id', 'capacity', 'student_left_age', 'student_right_age', 'focus_id', 'allow_remote_id', 'name'], 'safe'],
            [['authorSearch', 'branchSearch'], 'integer'],
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
        $query = TrainingProgramWork::find()->orderBy(['actual' => SORT_DESC]);

        /*if (array_key_exists("SearchTrainingProgram", $params))
        {
            if ($params["SearchTrainingProgram"]["authorSearch"] != null)
            {
                $authors = AuthorProgramWork::find()->where(['author_id' => $params["SearchTrainingProgram"]["authorSearch"]])->all();
                $aIds = [];
                foreach ($authors as $author) $aIds[] = $author->training_program_id;
                $query = $query->where(['IN', 'training_program.id', $aIds]);
            }

            if ($params["SearchTrainingProgram"]["branchSearch"] != null)
            {
                $branchs = BranchProgramWork::find()->where(['branch_id' => $params["SearchTrainingProgram"]["branchSearch"]])->all();
                $aIds = [];
                foreach ($branchs as $branch) $aIds[] = $branch->training_program_id;
                if ($params["SearchTrainingProgram"]["authorSearch"] != null)
                    $query = $query->andWhere(['IN', 'training_program.id', $aIds]);
                else
                    $query = $query->where(['IN', 'training_program.id', $aIds]);
            }
        }*/



        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
/*
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'ped_council_date' => $this->ped_council_date,
            'author_id' => $this->author_id,
            'capacity' => $this->capacity,
            'student_left_age' => $this->student_left_age,
            'student_right_age' => $this->student_right_age,
            'focus_id' => $this->focus_id,
            'allow_remote_id' => $this->allow_remote_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'ped_council_number', $this->ped_council_number]);*/
        return $dataProvider;
    }
}
