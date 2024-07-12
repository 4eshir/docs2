<?php

namespace common\models\search;

use common\models\work\document_in_out\DocumentInWork;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SearchDocumentIn represents the model behind the search form of `app\models\common\DocumentIn`.
 */
class SearchDocumentIn extends DocumentInWork
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'local_number', 'position_id', 'company_id', 'signed_id', 'get_id', 'creator_id', 'archive'], 'integer'],
            [['real_number', 'fullNumber'], 'string'],
            [['local_date', 'real_date', 'document_theme', 'target', 'scan', 'applications', 'key_words', 'correspondentName', 'companyName', 'sendMethodName',
                'start_date_search', 'finish_date_search'], 'safe'],
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
        $query = DocumentInWork::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['local_date' => SORT_DESC, 'local_number' => SORT_DESC, 'local_postfix' => SORT_DESC]]
        ]);

        return $dataProvider;
    }
}
