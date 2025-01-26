<?php


namespace frontend\models\work\educational\journal;

use common\Model;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\providers\group_lesson\TrainingGroupLessonProvider;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use InvalidArgumentException;
use Yii;

class VisitLesson extends Model
{
    private TrainingGroupLessonRepository $repository;

    public int $lessonId;
    public int $status;
    public $lesson;

    public function __construct(
        int $lessonId,
        int $status,
        TrainingGroupLessonRepository $repository = null,
        $config = []
    )
    {
        parent::__construct($config);
        $this->lessonId = $lessonId;
        $this->status = $status;
        if (!$repository) {
            $repository = Yii::createObject(
                TrainingGroupLessonRepository::class,
                ['provider' => Yii::createObject(TrainingGroupLessonProvider::class)]
            );
        }
        /** @var TrainingGroupLessonRepository $repository */
        $this->repository = $repository;

        $this->lesson = $this->repository->get($this->lessonId);
    }

    public function rules()
    {
        return [
            [['lessonId', 'status'], 'integer']
        ];
    }

    public function getLessonId()
    {
        return $this->lessonId;
    }

    /**
     * Раскладывает json-строку на массив VisitLesson
     * @param string $json
     * @return array
     */
    public static function fromString(string $json) : array
    {
        $lessonsArray = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException("Invalid JSON string.");
        }

        $visitLessons = [];
        foreach ($lessonsArray as $item) {
            $visitLessons[] = new VisitLesson($item['lesson_id'], $item['status']);
        }

        return $visitLessons;
    }

    /**
     * Склеивает массив VisitLesson json-строку
     * @param VisitLesson[] $visitLessons
     */
    public static function toString(array $visitLessons)
    {
        $newLessons = [];
        foreach ($visitLessons as $visitLesson) {
            $newLessons[] = (string)$visitLesson;
        }

        return '['.(implode(',', $newLessons)).']';
    }

    /**
     * @param VisitLesson[] $lessons
     * @return int[]
     */
    public static function getLessonIds(array $lessons)
    {
        return array_map(fn($obj) => $obj->getLessonId(), $lessons);
    }

    /**
     * @param VisitLesson[] $lessons
     * @param $lessonId
     * @return false|VisitLesson
     */
    public static function getLesson(array $lessons, $lessonId)
    {
        foreach ($lessons as $lesson) {
            if ($lesson->lessonId == $lessonId) {
                return $lesson;
            }
        }

        return false;
    }

    /**
     * Сравнивает два массива класса VisitLesson
     * @param VisitLesson[] $arr1
     * @param VisitLesson[] $arr2
     */
    public static function equalArrays(array $arr1, array $arr2)
    {
        $lessonIds1 = array_map(fn($lesson) => $lesson->lessonId, $arr1);
        $lessonIds2 = array_map(fn($lesson) => $lesson->lessonId, $arr2);

        $uniqueLessonIds1 = array_unique($lessonIds1);
        $uniqueLessonIds2 = array_unique($lessonIds2);

        return count($uniqueLessonIds1) === count($uniqueLessonIds2) &&
            count(array_intersect($uniqueLessonIds1, $uniqueLessonIds2)) === count($uniqueLessonIds1);
    }

    public function __toString()
    {
        return "{\"lesson_id\":$this->lessonId,\"status\":$this->status}";
    }

    public function getPrettyStatus()
    {
        switch ($this->status) {
            case VisitWork::NONE:
                return '--';
            case VisitWork::ATTENDANCE:
                return 'Я';
            case VisitWork::NO_ATTENDANCE:
                return 'Н';
            case VisitWork::DISTANCE:
                return 'Д';
        }
    }
}