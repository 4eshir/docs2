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
        $config = [])
    {
        $this->initCopyController = $initCopyController;
        $this->documentInOutCopyController = $documentInOutCopyController;
        $this->peopleTablesCopyController = $peopleTablesCopyController;
        $this->personalDataCopyController = $personalDataCopyController;
        $this->regulationCopyController = $regulationCopyController;
        $this->eventCopyController = $eventCopyController;
        $this->trainingProgramCopyController = $trainingProgramCopyController;
        parent::__construct($id, $module, $config);
    }

    public function actionCopyAll(){
        $this->initCopyController->actionCopyAll();
        $this->documentInOutCopyController->actionCopyAll();
        //$this->peopleTablesCopyController->actionCopyAll();
        $this->regulationCopyController->actionCopyAll();
        $this->personalDataCopyController->actionCopyAll();
        $this->eventCopyController->actionCopyAll();
        $this->trainingProgramCopyController->actionCopyAll();
    }
}