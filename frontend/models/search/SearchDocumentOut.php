<?php

namespace frontend\models\search;

use common\components\dictionaries\base\DocumentStatusDictionary;
use common\components\interfaces\SearchInterfaces;
use common\helpers\DateFormatter;
use frontend\models\search\abstractBase\DocumentSearch;
use frontend\models\work\document_in_out\DocumentOutWork;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class SearchDocumentOut extends DocumentSearch implements SearchInterfaces
{
    public $documentDate;       // дата документа
    public $sentDate;           // дата отправки документа


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['documentDate', 'number'], 'safe'],
        ]);
    }


    /**
     * Создает экземпляр DataProvider с учетом поискового запроса (фильтров или сортировки)
     *
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->load($params);
        $query = DocumentOutWork::find()
            ->joinWith([
                'company',
                'correspondent',
                'correspondent.people' => function ($query) {
                    $query->alias('correspondentPeople');
                },
                'inOutDocument.responsible' => function ($query) {
                    $query->alias('responsible');
                },
                'inOutDocument.responsible.people' => function ($query) {
                    $query->alias('responsiblePeople');
                },
                'executor' => function ($query) {
                    $query->alias('executor');
                },
                'executor.people' => function ($query) {
                    $query->alias('executorPeople');
                },
                'signed' => function ($query) {
                    $query->alias('signed');
                },
                'signed.people' => function ($query) {
                    $query->alias('signedPeople');
                }
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['document_date' => SORT_DESC, 'document_number' => SORT_DESC, 'document_postfix' => SORT_DESC]]
        ]);

        $this->sortAttributes($dataProvider);
        $this->filterQueryParams($query);

        return $dataProvider;
    }

    /**
     * Кастомизированная сортировка по полям таблицы, с учетом родительской сортировки
     *
     * @param ActiveDataProvider $dataProvider
     * @return void
     */
    public function sortAttributes(ActiveDataProvider $dataProvider) {
        parent::sortAttributes($dataProvider);

        $dataProvider->sort->attributes['documentDate'] = [
            'asc' => ['document_date' => SORT_ASC],
            'desc' => ['document_date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['executorName'] = [
            'asc' => ['people.firstname' => SORT_ASC],
            'desc' => ['people.firstname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['sentDate'] = [
            'asc' => ['send_date' => SORT_ASC],
            'desc' => ['send_date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['isAnswer'] = [
            'asc' => ['is_answer' => SORT_DESC],
            'desc' => ['is_answer' => SORT_ASC],
        ];
    }


    /**
     * Вызов функций фильтров по параметрам запроса
     *
     * @param ActiveQuery $query
     * @return void
     */
    public function filterQueryParams(ActiveQuery $query) {
        $this->filterDate($query);
        $this->filterNumber($query);
        $this->filterStatus($query);
        $this->filterExecutorName($query);
        $this->filterAbstractQueryParams($query, $this->documentTheme, $this->keyWords, $this->sendMethodName, $this->correspondentName);
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

            $query->andWhere([
                'or',
                ['between', 'document_date', $dateFrom, $dateTo],
                ['between', 'sent_date', $dateFrom, $dateTo],
            ]);
        }
    }

    /**
     * Фильтрация документа по заданному номеру
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterNumber(ActiveQuery $query) {
        $query->andFilterWhere(['like', "CONCAT(document_number, '/', document_postfix)", $this->number]);
    }

    /**
     * Фильтрует по статусу документа
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterStatus(ActiveQuery $query) {
        $statusConditions = [
            DocumentStatusDictionary::CURRENT => ['>=', 'document_date', date('Y') . '-01-01'],
            DocumentStatusDictionary::ARCHIVE => ['<=', 'document_date', date('Y-m-d')],
            DocumentStatusDictionary::RESERVED => ['like', 'LOWER(document_theme)', 'РЕЗЕРВ'],
            DocumentStatusDictionary::ANSWER => ['IS NOT', 'document_out_id', null],
        ];
        $query->andWhere($statusConditions[$this->status]);
    }

    /**
     * Фильтрует по исполнителю документа
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterExecutorName(ActiveQuery $query) {
        $query->andFilterWhere([
            'OR',
            ['like', 'LOWER(executorPeople.firstname)', mb_strtolower($this->executorName)],
            ['like', 'LOWER(signedPeople.firstname)', mb_strtolower($this->executorName)],
        ]);

    }
}