<?php

namespace common\components\compare;


use frontend\models\work\educational\training_group\GroupProjectsThemesWork;
use frontend\models\work\educational\training_group\TeacherGroupWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use InvalidArgumentException;

class GroupThemeCompare extends AbstractCompare
{
    public static function compare($c1, $c2): int
    {
        /** @var GroupProjectsThemesWork $c1 */
        /** @var GroupProjectsThemesWork $c2 */
        if (!(get_class($c1) === GroupProjectsThemesWork::class && get_class($c2) === GroupProjectsThemesWork::class)) {
            throw new InvalidArgumentException('Сравниваемые объекты не являются экземплярами класса GroupProjectsThemesWork');
        }

        $result = $c1->training_group_id <=> $c2->training_group_id;
        if ($result != 0) {
            return $result;
        }

        return $c1->project_theme_id <=> $c2->project_theme_id;
    }
}