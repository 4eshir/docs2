<?php

namespace frontend\services\educational;

use common\components\compare\TeacherGroupCompare;
use common\components\traits\CommonDatabaseFunctions;
use common\components\traits\Math;
use common\helpers\DateFormatter;
use common\helpers\files\filenames\TrainingGroupFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\models\scaffold\PeopleStamp;
use common\repositories\educational\TrainingGroupRepository;
use common\services\DatabaseService;
use common\services\general\files\FileService;
use common\services\general\PeopleStampService;
use DateTime;
use frontend\events\educational\training_group\CreateTeacherGroupEvent;
use frontend\events\educational\training_group\CreateTrainingGroupLessonEvent;
use frontend\events\educational\training_group\CreateTrainingGroupParticipantEvent;
use frontend\events\educational\training_group\DeleteTeacherGroupEvent;
use frontend\events\educational\training_group\DeleteTrainingGroupParticipantEvent;
use frontend\events\general\FileCreateEvent;
use frontend\forms\training_group\TrainingGroupBaseForm;
use frontend\forms\training_group\TrainingGroupParticipantForm;
use frontend\forms\training_group\TrainingGroupScheduleForm;
use frontend\models\work\educational\training_group\TeacherGroupWork;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;

class TrainingGroupService implements DatabaseService
{
    use CommonDatabaseFunctions, Math;

    private TrainingGroupRepository $trainingGroupRepository;
    private FileService $fileService;
    private TrainingGroupFileNameGenerator $filenameGenerator;
    private PeopleStampService $peopleStampService;

    public function __construct(
        TrainingGroupRepository $trainingGroupRepository,
        FileService $fileService,
        TrainingGroupFileNameGenerator $filenameGenerator,
        PeopleStampService $peopleStampService
    )
    {
        $this->trainingGroupRepository = $trainingGroupRepository;
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
            $newParticipants[] = TrainingGroupParticipantWork::fill($form->id, $participant->participant_id, $participant->send_method);
        }

        var_dump((string)(array_diff($newParticipants, $form->prevParticipants)[0]));
        var_dump((string)(array_diff($newParticipants, $form->prevParticipants)[1]));
        var_dump((string)(array_diff($newParticipants, $form->prevParticipants)[2]));
        var_dump('<br>');
        var_dump((string)($newParticipants[0]));
        var_dump((string)($newParticipants[1]));
        var_dump((string)($newParticipants[2]));
        $addParticipants = $this->setDifference($newParticipants, $form->prevParticipants);
        $delParticipants = $this->setDifference($form->prevParticipants, $newParticipants);


        foreach ($addParticipants as $participant) {
            $form->recordEvent(new CreateTrainingGroupParticipantEvent($form->id, $participant->participant_id, $participant->send_method), TrainingGroupParticipantWork::className());
        }

        foreach ($delParticipants as $participant) {
            $form->recordEvent(new DeleteTrainingGroupParticipantEvent($participant->id), TrainingGroupParticipantWork::className());
        }
    }

    public function attachLessons(TrainingGroupScheduleForm $form)
    {
        foreach ($form->lessons as $lesson) {
            /** @var TrainingGroupLessonWork $lesson */
            $form->recordEvent(
                new CreateTrainingGroupLessonEvent(
                    $form->id,
                    $lesson->lesson_date,
                    $lesson->lesson_start_time,
                    $lesson->branch,
                    $lesson->auditorium_id,
                    $lesson->lesson_end_time,
                    $lesson->duration
                ),
                TrainingGroupParticipantWork::className()
            );
        }
    }

    public function preprocessingLessons(TrainingGroupScheduleForm $formSchedule)
    {
        foreach ($formSchedule->lessons as $lesson) {
            /** @var TrainingGroupLessonWork $lesson */
            $lesson->duration = 1;
            $capacity = $formSchedule->trainingProgram->hour_capacity ?: 30;
            $lesson->lesson_end_time = ((new DateTime($lesson->lesson_start_time))->modify("+{$capacity} minutes"))->format('Y-m-d H:i:s');
        }
    }

}