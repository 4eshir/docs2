<?php

namespace backend\forms\report;

use common\Model;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\educational\TeacherGroupRepository;
use common\repositories\general\PeopleStampRepository;
use frontend\models\work\general\PeopleWork;
use yii\helpers\ArrayHelper;

class ManHoursReportForm extends Model
{
    // Тип отчета
    const MAN_HOURS_REPORT = 1;
    // Типы отчетов по обучающимся
    const PARTICIPANT_START_BEFORE_FINISH_IN = 2;
    const PARTICIPANT_START_IN_FINISH_AFTER = 3;
    const PARTICIPANT_START_IN_FINISH_IN = 4;
    const PARTICIPANT_START_BEFORE_FINISH_AFTER = 5;

    // Подтип отчета по обучающимся
    const PARTICIPANTS_ALL = 1;
    const PARTICIPANTS_UNIQUE = 2;

    // Подтип отчета по человеко-часам
    const MAN_HOURS_FAIR = 1; // учитываем неявки
    const MAN_HOURS_ALL = 2; // игнорируем неявки



    private TeacherGroupRepository $teacherGroupRepository;
    private PeopleStampRepository $peopleStampRepository;
    private PeopleRepository $peopleRepository;

    public $startDate;
    public $endDate;
    public $type;
    public $unic;
    /*
     * 0 - человеко-часы
     * 1 - всего уникальных людей
     * 2 - всего людей
     */
    public $branch;
    public $budget;
    public $teacher;
    public $focus;
    public $allowRemote;
    public $method;

    /**
     * @var PeopleWork[] $teachers
     */
    public array $teachers;

    public function __construct(
        TeacherGroupRepository $teacherGroupRepository,
        PeopleStampRepository $peopleStampRepository,
        PeopleRepository $peopleRepository,
        $config = []
    )
    {
        $this->teacherGroupRepository = $teacherGroupRepository;
        $this->peopleStampRepository = $peopleStampRepository;
        $this->peopleRepository = $peopleRepository;

        $teacherGroups = $this->teacherGroupRepository->getAll();
        $peopleStamps = $this->peopleStampRepository->getStamps(
            ArrayHelper::getColumn($teacherGroups, 'teacher_id')
        );

        $this->teachers = $this->peopleRepository->getByIds(
            ArrayHelper::getColumn($peopleStamps, 'people_id')
        );

        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['startDate', 'endDate'], 'string'],
            [['type', 'branch', 'budget', 'focus', 'allowRemote'], 'safe'],
            [['method', 'teacher', 'unic'], 'integer']
        ];
    }


    private function generateView($data, $type)
    {
        $result = '';

        if ($type == ManHoursReportForm::MAN_HOURS_REPORT)
        {
            $result .= '<tr><td>Количество человеко-часов за период с '.$this->start_date.' по '.$this->end_date.
                '</td><td>'.count($data).' ч/ч'.'</td></tr>';
        }
        else if ($type == ManHoursReportForm::PARTICIPANTS_REPORT)
        {
            $result .= $data[0] == -1 ? '' : '<tr><td><b>1</b></td><td>Количество обучающихся, начавших обучение до '.$this->start_date.' и завершивших обучение в период с '.$this->start_date.' по '.$this->end_date.'</td><td>'.$data[0]. ' чел.'.'</td></tr>';
            $result .= $data[1] == -1 ? '' : '<tr><td><b>2</b></td><td>Количество обучающихся, начавших обучение в период с '.$this->start_date.' по '.$this->end_date.' и завершивших обучение после '.$this->start_date.' по '.$this->end_date.'</td><td>'.$data[1]. ' чел.'.'</td></tr>';
            $result .= $data[2] == -1 ? '' : '<tr><td><b>3</b></td><td>Количество обучающихся, начавших обучение после '.$this->start_date.' и завершивших до '.$this->start_date.' по '.$this->end_date.'</td><td>'.$data[2]. ' чел.'.'</td></tr>';
            $result .= $data[3] == -1 ? '' : '<tr><td><b>4</b></td><td>Количество обучающихся, начавших обучение до '.$this->start_date.' и завершивших после '.$this->start_date.' по '.$this->end_date.'</td><td>'.$data[3]. ' чел.'.'</td></tr>';
        }
        else if ($type == ManHoursReportForm::PARTICIPANTS_UNIQUE_REPORT)
        {
            $result .= '<tr><td>Общее количество уникальных обучающихся</td><td>'.count($data).'</td></tr>';
        }

        return $result;
    }


    public function generateReport()
    {

    }


    public function save()
    {
        return true;
    }
}