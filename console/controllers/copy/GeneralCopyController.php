<?php

namespace console\controllers\copy;

use yii\console\Controller;

class GeneralCopyController extends Controller
{
    private InitCopyController $initCopyController;
    private DocumentInOutCopyController $documentInOutCopyController;
    private PeopleTablesCopyController $peopleTablesCopyController;
    private PersonalDataCopyController $personalDataCopyController;
    private RegulationCopyController $regulationCopyController;
    private EventCopyController $eventCopyController;
    private TrainingProgramCopyController $trainingProgramCopyController;
    private DocumentOrderCopyController $documentOrderCopyController;
    private LocalResponsibilityCopyController $localResponsibilityCopyController;
    private ForeignEventCopyController $foreignEventCopyController;
    private ActCopyController $actCopyController;
    public function __construct(
        $id,
        $module,
        InitCopyController $initCopyController,
        DocumentInOutCopyController $documentInOutCopyController,
        PeopleTablesCopyController $peopleTablesCopyController,
        PersonalDataCopyController $personalDataCopyController,
        RegulationCopyController $regulationCopyController,
        EventCopyController $eventCopyController,
        TrainingProgramCopyController $trainingProgramCopyController,
        DocumentOrderCopyController $documentOrderCopyController,
        LocalResponsibilityCopyController $localResponsibilityCopyController,
        ForeignEventCopyController $foreignEventCopyController,
        ActCopyController $actCopyController,
        $config = [])
    {
        $this->initCopyController = $initCopyController;
        $this->documentInOutCopyController = $documentInOutCopyController;
        $this->peopleTablesCopyController = $peopleTablesCopyController;
        $this->personalDataCopyController = $personalDataCopyController;
        $this->regulationCopyController = $regulationCopyController;
        $this->eventCopyController = $eventCopyController;
        $this->trainingProgramCopyController = $trainingProgramCopyController;
        $this->documentOrderCopyController = $documentOrderCopyController;
        $this->localResponsibilityCopyController = $localResponsibilityCopyController;
        $this->foreignEventCopyController = $foreignEventCopyController;
        $this->actCopyController = $actCopyController;
        parent::__construct($id, $module, $config);
    }

    public function actionCopyAll(){
        $this->actCopyController->actionCopyAll();
        $this->foreignEventCopyController->actionCopyAll();
        $this->localResponsibilityCopyController->actionCopyAll();
        $this->documentOrderCopyController->actionCopyAll();
        $this->initCopyController->actionCopyAll();
        $this->documentInOutCopyController->actionCopyAll();
        //$this->peopleTablesCopyController->actionCopyAll();
        $this->regulationCopyController->actionCopyAll();
        $this->personalDataCopyController->actionCopyAll();
        $this->eventCopyController->actionCopyAll();
        $this->trainingProgramCopyController->actionCopyAll();
    }
}