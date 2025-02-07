<?php

namespace frontend\forms\participants;


use common\events\EventTrait;
use common\repositories\dictionaries\ForeignEventParticipantsRepository;
use common\repositories\dictionaries\PersonalDataParticipantRepository;
use common\repositories\educational\TeacherGroupRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\educational\VisitRepository;
use common\repositories\event\ParticipantAchievementRepository;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\educational\journal\VisitWork;
use Yii;

class MergeParticipantForm extends \yii\base\Model
{
    use EventTrait;

    public $fio1;
    public $fio2;
    public $id1;
    public $id2;

    public $firstname;
    public $surname;
    public $patronymic;
    public $sex;
    public $pd = [];

    public $targetId;
    public MergeParticipantFormData $data;

    public ForeignEventParticipantsWork $editModel;

    private ForeignEventParticipantsRepository $participantRepository;
    private TrainingGroupParticipantRepository $participantGroupRepository;
    private TeacherGroupRepository $teacherGroupRepository;
    private ParticipantAchievementRepository $participantAchievementRepository;
    private VisitRepository $visitRepository;

    private ForeignEventParticipantsRepository $participantsRepository;

    public function __construct(
        ForeignEventParticipantsRepository $participantRepository,
        TrainingGroupParticipantRepository $participantGroupRepository,
        TeacherGroupRepository $teacherGroupRepository,
        ParticipantAchievementRepository $participantAchievementRepository,
        VisitRepository $visitRepository,
        $config = []
    )
    {
        parent::__construct($config);
        $this->participantRepository = $participantRepository;
        $this->participantGroupRepository = $participantGroupRepository;
        $this->teacherGroupRepository = $teacherGroupRepository;
        $this->participantAchievementRepository = $participantAchievementRepository;
        $this->visitRepository = $visitRepository;

        $this->editModel = Yii::createObject(ForeignEventParticipantsWork::class);
        $this->data = new MergeParticipantFormData(
            $this->participantRepository->getParticipantsForMerge(),
        );
    }

    public function rules()
    {
        return [
            [['firstname', 'surname', 'patronymic', 'fio1', 'fio2'], 'string'],
            [['pd', 'editModel'], 'safe'],
            [['sex', 'targetId', 'id1', 'id2', 'guaranted_true'], 'integer'],
        ];
    }

    public function save()
    {
        //получаем учебные группы второго участника

        $tps = TrainingGroupParticipantWork::find()->where(['participant_id' => $this->id2])->all();
        foreach ($tps as $tp)
        {
            $tp->participant_id = $this->id1;
            $tp->save();
        }

        //-----------------------------------------

        //получаем мероприятия второго участника

        $tps = TeacherParticipantWork::find()->where(['participant_id' => $this->id2])->all();
        foreach ($tps as $tp)
        {
            $tp->participant_id = $this->id1;
            $tp->save();
        }

        //-----------------------------------------

        //получаем достижения второго участника

        /*$tps = ParticipantAchievementWork::find()->joinWith(['teacherParticipant teacherParticipant'])->where(['teacherParticipant.participant_id' => $this->id2])->all();
        foreach ($tps as $tp)
        {
            $tp->participant_id = $this->id1;
            $tp->save();
        }*/

        //-----------------------------------------

        //получаем файлы второго участника

        /*$tps = ParticipantFilesWork::find()->joinWith(['teacherParticipant teacherParticipant'])->where(['teacherParticipant.participant_id' => $this->id2])->all();
        foreach ($tps as $tp)
        {
            $tp->participant_id = $this->id1;
            $tp->save();
        }*/

        //-----------------------------------------

        //получаем явки/неявки второго участника

        $tps = VisitWork::find()->where(['foreign_event_participant_id' => $this->id2])->all();
        foreach ($tps as $tp)
        {
            $tp->foreign_event_participant_id = $this->id1;
            $tp->save(false);
        }

        //-----------------------------------------

        //Получаем связку с педагогом второго участника

        /*$tps = TeacherParticipantWork::find()->where(['participant_id' => $this->id2])->all();
        foreach ($tps as $tp)
        {
            $tp->participant_id = $this->id1;
            $tp->save(false);
        }*/

        //-----------------------------------------

        //удаляем второго участника

        $part = ForeignEventParticipantsWork::find()->where(['id' => $this->id2])->one();
        $part->delete();

        //-------------------------

        //сохраняем данные по первому участнику

        $part = ForeignEventParticipantsWork::find()->where(['id' => $this->id1])->one();
        $part->firstname = $this->edit_model->firstname;
        $part->secondname = $this->edit_model->secondname;
        $part->patronymic = $this->edit_model->patronymic;
        $part->birthdate = $this->edit_model->birthdate;
        $part->sex = $this->edit_model->sex;
        $part->guaranted_true = 1;

        $part->save();

        $data = PersonalDataWork::find()->all();
        foreach ($data as $one)
        {
            $partData = PersonalDataForeignEventParticipantWork::find()->where(['foreign_event_participant_id' => $this->id1])->andWhere(['personal_data_id' => $one->id])->one();
            if ($partData === null)
            {
                $partData = new PersonalDataForeignEventParticipantWork();
                $partData->foreign_event_participant_id = $this->id1;
                $partData->personal_data_id = $one->id;
            }
            if ($this->edit_model->pd !== "" && array_search($one->id, $this->edit_model->pd) !== false)
                $partData->status = 1;
            else
                $partData->status = 0;
            $partData->save();
        }


        //-------------------------------------
    }
}