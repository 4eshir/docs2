<?php

namespace frontend\models\search\abstractBase;

use common\components\dictionaries\base\DocumentStatusDictionary;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

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
    public $correspondentName;  // корреспондент (отправитель) фио или организация
    public $number;             // номер документа (регистрационный или присвоенный нами)

    public function rules()
    {
        return [
            [['id', 'positionId', 'companyId', 'signedId', 'getId', 'creatorId'], 'integer'],
            [['fullNumber', 'keyWords'], 'string'],
            [['startDateSearch', 'finishDateSearch'], 'date', 'format' => 'dd.MM.yyyy'],
            [['documentTheme', 'companyName', 'sendMethodName', 'executorName', 'status', 'correspondentName', 'number'], 'safe'],
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
        string $keyWords = null,
        string $correspondentName = null,
        string $number = null
    ) {
        parent::__construct();
        $this->fullNumber = $fullNumber;
        $this->companyName = $companyName;
        $this->sendMethodName = $sendMethodName;
        $this->documentTheme = $documentTheme;
        $this->startDateSearch = $startDateSearch;
        $this->finishDateSearch = $finishDateSearch;
        $this->executorName = $executorName;
        $this->status = $status == null ? DocumentStatusDictionary::CURRENT : $status;
        $this->keyWords = $keyWords;
        $this->correspondentName = $correspondentName;
        $this->number = $number;
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
     * @param ActiveQuery $query
     * @param $documentTheme
     * @param $keyWords
     * @param $sendMethodName
     * @return void
     */
    public function filterAbstractQueryParams(ActiveQuery $query, $documentTheme, $keyWords, $sendMethodName, $correspondentName) {
        $this->filterTheme($query, $documentTheme);
        $this->filterKeyWords($query, $keyWords);
        $this->filterSendMethodName($query, $sendMethodName);
        $this->filterCorrespondentName($query, $correspondentName);
    }

    /**
     * Фильтрует по теме документа
     *
     * @param ActiveQuery $query
     * @param $documentTheme
     * @return void
     */
    private function filterTheme(ActiveQuery $query, $documentTheme) {
        $query->andFilterWhere(['like', 'LOWER(document_theme)', mb_strtolower($documentTheme)]);
    }

    /**
     * Фильтрует по ключевым словам
     *
     * @param ActiveQuery $query
     * @param $keyWords
     * @return void
     */
    private function filterKeyWords(ActiveQuery $query, $keyWords) {
        $query->andFilterWhere(['like', 'LOWER(key_words)', mb_strtolower($keyWords)]);
    }

    /**
     * Фильтрует по методу получения письма
     *
     * @param ActiveQuery $query
     * @param $sendMethodName
     * @return void
     */
    private function filterSendMethodName(ActiveQuery $query, $sendMethodName) {
        $query->andFilterWhere(['like', 'send_method', $sendMethodName]);
    }

    /**
     * Фильтрация документов любому из полей "Ф И О" корреспондента
     *
     * @param ActiveQuery $query
     * @param $correspondentName
     * @return void
     */
    private function filterCorrespondentName(ActiveQuery $query, $correspondentName) {
        $lowerCorrespondentName = mb_strtolower($correspondentName);
        $query->andFilterWhere(['or',
            ['like', 'LOWER(company.name)', $lowerCorrespondentName],
            ['like', 'LOWER(company.short_name)', $lowerCorrespondentName],
            ['like', 'LOWER(correspondentPeople.firstname)', $lowerCorrespondentName],
        ]);
    }
}