<?php

namespace frontend\models\search\abstractBase;

use yii\base\Model;

class DocumentSearch extends Model
{
    public $fullNumber;         // составной номер документа (может содержать символ '/' )
    public $companyName;        // организация - отправитель или получатель письма
    public $sendMethodName;     // способ отправки или получения письма
    public $documentTheme;      // тема документа
    public $startDateSearch;    // стартовая дата поиска документов
    public $finishDateSearch;   // конечная дата поиска документов
    public $executorName;       // исполнитель письма
    public $status;             // статус документа (архивное, требуется ответ, отвеченное, и т.д.)
    public $keyWords;           // ключевые слова

    public function rules()
    {
        return [
            [['id', 'positionId', 'companyId', 'signedId', 'getId', 'creatorId'], 'integer'],
            [['fullNumber', 'keyWords'], 'string'],
            [['startDateSearch', 'finishDateSearch'], 'date', 'format' => 'dd.MM.yyyy'],
            [['documentTheme', 'companyName', 'sendMethodName', 'executorName', 'status'], 'safe'],
        ];
    }

    public function __construct(
        string $fullNumber = null,
        string $companyName = null,
        int $sendMethodName = null,
        string $documentTheme = null,
        string $startDateSearch = null,
        string $finishDateSearch = null,
        string $executorName = null,
        int $status = null,
        string $keyWords = null
    ) {
        parent::__construct();
        $this->fullNumber = $fullNumber;
        $this->companyName = $companyName;
        $this->sendMethodName = $sendMethodName;
        $this->documentTheme = $documentTheme;
        $this->startDateSearch = $startDateSearch;
        $this->finishDateSearch = $finishDateSearch;
        $this->executorName = $executorName;
        $this->status = $status;
        $this->keyWords = $keyWords;
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
    public function sortAttributes($dataProvider) {
        $dataProvider->sort->attributes['fullNumber'] = [
            'asc' => ['local_number' => SORT_ASC, 'local_postfix' => SORT_ASC],
            'desc' => ['local_number' => SORT_DESC, 'local_postfix' => SORT_DESC],
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
    }

    /**
     * Вызов функций фильтров по параметрам запроса
     *
     * @param $query
     * @param $documentTheme
     * @param $keyWords
     * @param $executorName
     * @param $sendMethodName
     * @return void
     */
    public function filterAbstractQueryParams($query, $documentTheme, $keyWords, $executorName, $sendMethodName) {
        $this->filterTheme($query, $documentTheme);
        $this->filterKeyWords($query, $keyWords);
        $this->filterExecutorName($query, $executorName);
        $this->filterSendMethodName($query, $sendMethodName);
    }

    /**
     * Фильтрует по теме документа
     *
     * @param $query
     * @param $documentTheme
     * @return void
     */
    private function filterTheme($query, $documentTheme) {
        $query->andFilterWhere(['like', 'document_theme', $documentTheme]);
    }

    /**
     * Фильтрует по ключевым словам
     *
     * @param $query
     * @param $keyWords
     * @return void
     */
    private function filterKeyWords($query, $keyWords) {
        $query->andFilterWhere(['like', 'LOWER(key_words)', mb_strtolower($keyWords)]);
    }

    /**
     * Фильтрует по исполнителю документа
     *
     * @param $query
     * @param $executorName
     * @return void
     */
    private function filterExecutorName($query, $executorName) {
        $query->andFilterWhere(['like', 'LOWER(people.firstname)', mb_strtolower($executorName)]);
    }

    /**
     * Фильтрует по методу получения письма
     *
     * @param $query
     * @param $sendMethodName
     * @return void
     */
    private function filterSendMethodName($query, $sendMethodName) {
        $query->andFilterWhere(['like', 'send_method', $sendMethodName]);
    }
}