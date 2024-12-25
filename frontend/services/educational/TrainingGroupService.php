<?php

namespace frontend\services\educational;

use common\components\compare\GroupExpertCompare;
use common\components\compare\GroupThemeCompare;
use common\components\compare\LessonGroupCompare;
use common\components\compare\ParticipantGroupCompare;
use common\components\compare\TeacherGroupCompare;
use common\components\traits\CommonDatabaseFunctions;
use common\components\traits\Math;
use common\helpers\DateFormatter;
use common\helpers\files\filenames\TrainingGroupFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\models\scaffold\PeopleStamp;
use common\repositories\dictionaries\AuditoriumRepository;
use common\repositories\educational\ProjectThemeRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\services\DatabaseService;
use common\services\general\files\FileService;
use common\services\general\PeopleStampService;
use DateTime;
use frontend\events\educational\training_group\AddGroupExpertEvent;
use frontend\events\educational\training_group\AddGroupThemeEvent;
use frontend\events\educational\training_group\CreateLessonGroupEvent;
use frontend\events\educational\training_group\CreateTeacherGroupEvent;
use frontend\events\educational\training_group\CreateTrainingGroupLessonEvent;
use frontend\events\educational\training_group\CreateTrainingGroupParticipantEvent;
use frontend\events\educational\training_group\DeleteGroupExpertEvent;
use frontend\events\educational\training_group\DeleteGroupThemeEvent;
use frontend\events\educational\training_group\DeleteLessonGroupEvent;
use frontend\events\educational\training_group\DeleteTeacherGroupEvent;
use frontend\events\educational\training_group\DeleteTrainingGroupParticipantEvent;
use frontend\events\educational\training_group\UpdateGroupExpertEvent;
use frontend\events\educational\training_group\UpdateProjectThemeEvent;
use frontend\events\educational\training_group\UpdateTrainingGroupParticipantEvent;
use frontend\events\general\FileCreateEvent;
use frontend\forms\training_group\PitchGroupForm;
use frontend\forms\training_group\TrainingGroupBaseForm;
use frontend\forms\training_group\TrainingGroupParticipantForm;
use frontend\forms\training_group\TrainingGroupScheduleForm;
use frontend\models\work\educational\training_group\GroupProjectsThemesWork;
use frontend\models\work\educational\training_group\TeacherGroupWork;
use frontend\models\work\educational\training_group\TrainingGroupExpertWork;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\ProjectThemeWork;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\UploadedFile;

class TrainingGroupService implements DatabaseService
{
    use CommonDatabaseFunctions, Math;

    private TrainingGroupRepository $trainingGroupRepository;
    private ProjectThemeRepository $themeRepository;
    private FileService $fileService;
    private TrainingGroupFileNameGenerator $filenameGenerator;
    private PeopleStampService $peopleStampService;

    public function __construct(
        TrainingGroupRepository $trainingGroupRepository,
        ProjectThemeRepository $themeRepository,
        FileService $fileService,
        TrainingGroupFileNameGenerator $filenameGenerator,
        PeopleStampService $peopleStampService
    )
    {
        $this->trainingGroupRepository = $trainingGroupRepository;
        $this->themeRepository = $themeRepository;
        $this->fileService = $fileService;
        $this->filenameGenerator = $filenameGenerator;
        $this->peopleStampService = $peopleStampService;
    }

    public function convertBaseFormToModel(TrainingGroupBaseForm $form)
    {
        if ($form->id !== null) {
            $entity = $this->trainingGroupRepository->get($form->id);
        }
        else {
            $entity = new TrainingGroupWork();
        }
        $entity->branch = $form->branch;
        $entity->training_program_id = $form->trainingProgramId;
        $entity->budget = $form->budget;
        $entity->is_network = $form->network;
        $entity->start_date = $form->startDate;
        $entity->finish_date = $form->endDate;
        $entity->order_stop = $form->endLoadOrders;

        return $entity;
    }

    public function getFilesInstances(TrainingGroupBaseForm $form)
    {
        $form->photos = UploadedFile::getInstances($form, 'photos');
        $form->presentations = UploadedFile::getInstances($form, 'presentations');
        $form->workMaterials = UploadedFile::getInstances($form, 'workMaterials');
    }

    public function saveFilesFromModel(TrainingGroupBaseForm $form)
    {
        for ($i = 1; $i < count($form->photos) + 1; $i++) {
            $filename = $this->filenameGenerator->generateFileName($form, FilesHelper::TYPE_PHOTO, ['counter' => $i]);

            $this->fileService->uploadFile(
                $form->photos[$i - 1],
                $filename,
                [
                    'tableName' => TrainingGroupWork::tableName(),
                    'fileType' => FilesHelper::TYPE_PHOTO
                ]
            );

            $form->recordEvent(
                new FileCreateEvent(
                    TrainingGroupWork::tableName(),
                    $form->id,
                    FilesHelper::TYPE_PHOTO,
                    $filename,
                    FilesHelper::LOAD_TYPE_MULTI
                ),
                TrainingGroupWork::tableName()
            );
        }

        for ($i = 1; $i < count($form->presentations) + 1; $i++) {
            $filename = $this->filenameGenerator->generateFileName($form, FilesHelper::TYPE_PRESENTATION, ['counter' => $i]);

            $this->fileService->uploadFile(
                $form->presentations[$i - 1],
                $filename,
                [
                    'tableName' => TrainingGroupWork::tableName(),
                    'fileType' => FilesHelper::TYPE_PRESENTATION
                ]
            );

            $form->recordEvent(
                new FileCreateEvent(
                    TrainingGroupWork::tableName(),
                    $form->id,
                    FilesHelper::TYPE_PRESENTATION,
                    $filename,
                    FilesHelper::LOAD_TYPE_MULTI
                ),
                TrainingGroupWork::tableName()
            );
        }

        for ($i = 1; $i < count($form->workMaterials) + 1; $i++) {
            $filename = $this->filenameGenerator->generateFileName($form, FilesHelper::TYPE_WORK, ['counter' => $i]);

            $this->fileService->uploadFile(
                $form->workMaterials[$i - 1],
                $filename,
                [
                    'tableName' => TrainingGroupWork::tableName(),
                    'fileType' => FilesHelper::TYPE_WORK
                ]
            );

            $form->recordEvent(
                new FileCreateEvent(
                    TrainingGroupWork::tableName(),
                    $form->id,
                    FilesHelper::TYPE_WORK,
                    $filename,
                    FilesHelper::LOAD_TYPE_MULTI
                ),
                TrainingGroupWork::tableName()
            );
        }
    }

    public function getUploadedFilesTables(TrainingGroupBaseForm $form)
    {
        if ($form->id == null) {
            return [
                'photos' => '',
                'presentations' => '',
                'workMaterials' => '',
            ];
        }
        $model = $this->trainingGroupRepository->get($form->id);
        /** @var TrainingGroupWork $otherLinks */
        $photoLinks = $model->getFileLinks(FilesHelper::TYPE_PHOTO);
        $photoFiles = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($photoLinks, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($photoLinks), $model->id), 'fileId' => ArrayHelper::getColumn($photoLinks, 'id')])
            ]
        );

        $presentationLinks = $model->getFileLinks(FilesHelper::TYPE_PRESENTATION);
        $presentationFiles = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($presentationLinks, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($presentationLinks), $model->id), 'fileId' => ArrayHelper::getColumn($presentationLinks, 'id')])
            ]
        );

        $workMaterialsLinks = $model->getFileLinks(FilesHelper::TYPE_WORK);
        $workMaterialsFiles = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($workMaterialsLinks, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($workMaterialsLinks), $model->id), 'fileId' => ArrayHelper::getColumn($workMaterialsLinks, 'id')])
            ]
        );

        return [
            'photos' => $photoFiles,
            'presentations' => $presentationFiles,
            'workMaterials' => $workMaterialsFiles
        ];
    }

    public function isAvailableDelete($id)
    {
        /*$docsIn = $this->documentInRepository->checkDeleteAvailable(DocumentIn::tableName(), Company::tableName(), $entityId);
        $docsOut = $this->documentOutRepository->checkDeleteAvailable(DocumentOut::tableName(), Company::tableName(), $entityId);
        $people = $this->peoplePositionCompanyBranchRepository->checkDeleteAvailable(PeoplePositionCompanyBranch::tableName(), Company::tableName(), $entityId);
        $peopleStamp = $this->peopleStampRepository->checkDeleteAvailable(PeopleStamp::tableName(), Company::tableName(), $entityId);

        return array_merge($docsIn, $docsOut, $people, $peopleStamp);*/
        return [];
    }

    public function attachTeachers(TrainingGroupBaseForm $form, array $modelTeachers)
    {
        $newTeachers = [];
        foreach ($modelTeachers as $teacher) {
            /** @var PeopleStamp $teacherStamp */
            /** @var TeacherGroupWork $teacher */
            $teacherStamp = $this->peopleStampService->createStampFromPeople($teacher->peopleId);
            $newTeachers[] = TeacherGroupWork::fill($teacherStamp, $form->id);
        }
        $newTeachers = array_unique($newTeachers);

        $addTeachers = $this->setDifference($newTeachers, $form->prevTeachers, TeacherGroupCompare::class);
        $delTeachers = $this->setDifference($form->prevTeachers, $newTeachers, TeacherGroupCompare::class);

        foreach ($addTeachers as $teacher) {
            $form->recordEvent(new CreateTeacherGroupEvent($form->id, $teacher->teacher_id), TrainingGroupWork::className());
        }

        foreach ($delTeachers as $teacher) {
            $form->recordEvent(new DeleteTeacherGroupEvent($teacher->id), TrainingGroupWork::className());
        }
    }

    public function attachParticipants(TrainingGroupParticipantForm $form)
    {
        $newParticipants = [];
        foreach ($form->participants as $participant) {
            /** @var TrainingGroupParticipantWork $participant */
            $newParticipants[] = TrainingGroupParticipantWork::fill(
                $form->id,
                $participant->participant_id,
                $participant->send_method,
                $participant->id ? : null
            );
        }
        $newParticipants = array_unique($newParticipants);

        $addParticipants = $this->setDifference($newParticipants, $form->prevParticipants, ParticipantGroupCompare::class);
        $delParticipants = $this->setDifference($form->prevParticipants, $newParticipants, ParticipantGroupCompare::class);


        foreach ($addParticipants as $participant) {
            $form->recordEvent(new CreateTrainingGroupParticipantEvent($form->id, $participant->participant_id, $participant->send_method), TrainingGroupParticipantWork::className());
        }

        foreach ($delParticipants as $participant) {
            $form->recordEvent(new DeleteTrainingGroupParticipantEvent($participant->id), TrainingGroupParticipantWork::className());
        }

        foreach ($newParticipants as $participant) {
            if ($participant->id !== null) {
                $form->recordEvent(new UpdateTrainingGroupParticipantEvent($participant->id, $participant->participant_id, $participant->send_method), TrainingGroupParticipantWork::className());
            }
        }
    }

    public function attachLessons(TrainingGroupScheduleForm $form)
    {
        $newLessons = [];
        foreach ($form->lessons as $lesson) {
            /** @var TrainingGroupLessonWork $lesson */
            $lessonEntity = TrainingGroupLessonWork::fill(
                $form->id,
                $lesson->lesson_date,
                $lesson->lesson_start_time,
                $lesson->branch,
                $lesson->auditorium_id,
                $lesson->lesson_end_time,
                $lesson->duration
            );
            if ($lessonEntity->isEnoughData()) {
                $newLessons[] = $lessonEntity;
            }
        }
        $newLessons = array_unique($newLessons);
        
        $addLessons = $this->setDifference($newLessons, $form->prevLessons, LessonGroupCompare::class);
        $delLessons = $this->setDifference($form->prevLessons, $newLessons, LessonGroupCompare::class);

        foreach ($addLessons as $lesson) {
            $form->recordEvent(new CreateLessonGroupEvent(
                $lesson->lesson_date,
                $lesson->lesson_start_time,
                $lesson->lesson_end_time,
                $lesson->duration,
                $lesson->branch,
                $lesson->auditorium_id,
                $form->id
            ),
            TrainingGroupLessonWork::className());
        }

        foreach ($delLessons as $lesson) {
            $form->recordEvent(new DeleteLessonGroupEvent($lesson->id), TrainingGroupLessonWork::className());
        }
    }

    public function createNewThemes(PitchGroupForm $form)
    {
        $themeIds = [];
        foreach ($form->themes as $theme) {
            $themeIds[] = $this->themeRepository->save(
                ProjectThemeWork::fill(
                    $theme->name,
                    $theme->project_type,
                    $theme->description
                )
            );
        }

        $form->themeIds = $themeIds;
    }

    public function attachThemes(PitchGroupForm $form)
    {
        $newThemes = [];
        foreach ($form->themeIds as $themeId) {
            $groupThemeEntity = GroupProjectsThemesWork::fill(
                $form->id,
                $themeId,
                0
            );
            $newThemes[] = $groupThemeEntity;
        }
        $newThemes = array_unique($newThemes);

        $addThemes = $this->setDifference($newThemes, $form->prevThemes, GroupThemeCompare::class);
        $delThemes = $this->setDifference($form->prevThemes, $newThemes, GroupThemeCompare::class);

        foreach ($addThemes as $theme) {
            /** @var GroupProjectsThemesWork $theme */
            $form->recordEvent(new AddGroupThemeEvent($form->id, $theme->project_theme_id, $theme->confirm), GroupProjectsThemesWork::class);
        }

        foreach ($delThemes as $theme) {
            /** @var GroupProjectsThemesWork $theme */
            $form->recordEvent(new DeleteGroupThemeEvent($theme->id), GroupProjectsThemesWork::class);
        }

        foreach ($newThemes as $theme) {
            /** @var GroupProjectsThemesWork $theme */
            $form->recordEvent(
                new UpdateProjectThemeEvent(
                    $theme->project_theme_id,
                    $theme->projectThemeWork->project_type,
                    $theme->projectThemeWork->description
                ),
                GroupProjectsThemesWork::class
            );
        }
    }

    public function attachExperts(PitchGroupForm $form)
    {
        $newExperts = [];
        foreach ($form->experts as $expert) {
            $peopleStampId = $this->peopleStampService->createStampFromPeople($expert->expertId);
            $groupExpertEntity = TrainingGroupExpertWork::fill(
                $form->id,
                $peopleStampId,
                $expert->expert_type,
                $expert->id !== '' ? : null,
            );
            $newExperts[] = $groupExpertEntity;
        }
        $newExperts = array_unique($newExperts);

        $addExperts = $this->setDifference($newExperts, $form->prevExperts, GroupExpertCompare::class);
        $delExperts = $this->setDifference($form->prevExperts, $newExperts, GroupExpertCompare::class);

        foreach ($addExperts as $expert) {
            /** @var TrainingGroupExpertWork $expert */
            $form->recordEvent(new AddGroupExpertEvent($form->id, $expert->expert_id, $expert->expert_type), TrainingGroupExpertWork::class);
        }

        foreach ($delExperts as $expert) {
            /** @var TrainingGroupExpertWork $expert */
            $form->recordEvent(new DeleteGroupExpertEvent($expert->id), TrainingGroupExpertWork::class);
        }

        foreach ($newExperts as $expert) {
            if ($expert->id !== null) {
                $form->recordEvent(new UpdateGroupExpertEvent($expert->id, $expert->expert_id, $expert->expert_type), TrainingGroupExpertWork::class);
            }
        }
    }

    public function preprocessingLessons(TrainingGroupScheduleForm $formSchedule)
    {
        foreach ($formSchedule->lessons as $lesson) {
            /** @var TrainingGroupLessonWork $lesson */
            $lesson->duration = 1;
            $capacity = $formSchedule->trainingProgram->hour_capacity ?: 40;
            $lesson->lesson_end_time = ((new DateTime($lesson->lesson_start_time))->modify("+{$capacity} minutes"))->format('H:i:s');
            $lesson->lesson_start_time = (new DateTime($lesson->lesson_start_time))->format('H:i:s');
        }
    }

    public function prepareFormScheduleData($id)
    {
        $formSchedule = new TrainingGroupScheduleForm($id);
        $auditoriums = (Yii::createObject(AuditoriumRepository::class))->getAll();
        $scheduleTable = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Дата занятия'], ArrayHelper::getColumn($formSchedule->prevLessons, 'lesson_date')),
                array_merge(['Время начала'], ArrayHelper::getColumn($formSchedule->prevLessons, 'lesson_start_time')),
                array_merge(['Время окончания'], ArrayHelper::getColumn($formSchedule->prevLessons, 'lesson_end_time')),
                array_merge(['Помещение'], ArrayHelper::getColumn($formSchedule->prevLessons, 'auditoriumName'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Редактировать',
                    Url::to('update-lesson'),
                    [
                        'groupId' => array_fill(0, count($formSchedule->prevLessons), $formSchedule->id),
                        'entityId' => ArrayHelper::getColumn($formSchedule->prevLessons, 'id')
                    ]
                ),
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-lesson'),
                    [
                        'groupId' => array_fill(0, count($formSchedule->prevLessons), $formSchedule->id),
                        'entityId' => ArrayHelper::getColumn($formSchedule->prevLessons, 'id')
                    ]
                )
            ]
        );

        $scheduleTable = HtmlBuilder::wrapTableInCheckboxesColumn(
            Url::to(['group-deletion', 'id' => $formSchedule->id]),
            'Удалить выбранные',
            'check[]',
            ArrayHelper::getColumn($formSchedule->prevLessons, 'id'),
            $scheduleTable
        );

        return [
            'formSchedule' => $formSchedule,
            'modelLessons' => [new TrainingGroupLessonWork],
            'auditoriums' => $auditoriums,
            'scheduleTable' => $scheduleTable
        ];
    }
}