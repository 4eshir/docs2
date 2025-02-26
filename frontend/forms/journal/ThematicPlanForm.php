<?php

namespace frontend\forms\journal;

use common\Model;
use common\repositories\educational\LessonThemeRepository;
use common\repositories\providers\lesson_theme\LessonThemeProvider;
use frontend\models\work\educational\training_group\LessonThemeWork;
use Yii;

class ThematicPlanForm extends Model
{
    private LessonThemeRepository $lessonThemeRepository;

    public int $groupId;

    /** @var LessonThemeWork[] $lessonThemes */
    public array $lessonThemes;

    public function __construct(
        int $groupId,
        LessonThemeRepository $lessonThemeRepository = null,
        $config = []
    )
    {
        parent::__construct($config);
        $this->groupId = $groupId;
        if (!$lessonThemeRepository) {
            $lessonThemeRepository = Yii::createObject(
                LessonThemeRepository::class,
                ['provider' => Yii::createObject(LessonThemeProvider::class)]
            );
        }

        $this->lessonThemeRepository = $lessonThemeRepository;
        $this->lessonThemes = $this->lessonThemeRepository->getByTrainingGroupId($this->groupId);
    }


}