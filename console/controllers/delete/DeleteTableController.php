<?php

namespace console\controllers\delete;

use Yii;
use yii\console\Controller;

class DeleteTableController extends Controller
{
    public function actionDeleteBotMessage(){
        Yii::$app->db->createCommand()->delete('bot_message')->execute();
    }
    public function actionDeleteCertificateTemplates(){
        Yii::$app->db->createCommand()->delete('certificate_templates')->execute();
    }
    public function actionDeleteCharacteristicObject(){
        Yii::$app->db->createCommand()->delete('characteristic_object')->execute();
    }
    public function actionDeleteComplex(){
        Yii::$app->db->createCommand()->delete('complex')->execute();
    }
    public function actionDeleteEntry(){
        Yii::$app->db->createCommand()->delete('entry')->execute();
    }
    public function actionDeleteErrors(){
        Yii::$app->db->createCommand()->delete('errors')->execute();
    }
    public function actionDeleteForeignEventParticipants(){
        Yii::$app->db->createCommand()->delete('foreign_event_participants')->execute();
    }
    public function actionDeletePatchnotes(){
        Yii::$app->db->createCommand()->delete('patchnotes')->execute();
    }
    public function actionDeletePosition(){
        Yii::$app->db->createCommand()->delete('position')->execute();
    }
    public function actionDeleteProductUnion(){
        Yii::$app->db->createCommand()->delete('product_union')->execute();
    }
    public function actionDeleteProjectTheme(){
        Yii::$app->db->createCommand()->delete('project_theme')->execute();
    }
    public function actionDeleteRussianNames(){
        Yii::$app->db->createCommand()->delete('russian_names')->execute();
    }
    public function actionDeleteCompany(){
        Yii::$app->db->createCommand()->delete('company')->execute();
    }
    public function actionDeletePeople(){
        Yii::$app->db->createCommand()->delete('people')->execute();
    }
    public function actionDeleteUser(){
        Yii::$app->db->createCommand()->delete('user')->execute();
    }
    public function actionDeleteDocumentIn(){
        Yii::$app->db->createCommand()->delete('document_in')->execute();
    }
    public function actionDeleteDocumentOut(){
        Yii::$app->db->createCommand()->delete('document_out')->execute();
    }
    public function actionDeleteInOutDocuments(){
        Yii::$app->db->createCommand()->delete('in_out_documents')->execute();
    }
    public function actionDeleteFiles()
    {
        Yii::$app->db->createCommand()->delete('files')->execute();
    }
    public function actionDeletePeoplePositionCompanyBranch(){
        Yii::$app->db->createCommand()->delete('people_position_company_branch')->execute();
    }
    public function actionDeleteRegulation(){
        Yii::$app->db->createCommand()->delete('regulation')->execute();
    }
    public function actionDeletePersonalData(){
        Yii::$app->db->createCommand()->delete('personal_data_participant')->execute();
    }
    public function actionDeleteAll()
    {
        $this->actionDeletePersonalData();

        $this->actionDeleteRegulation();

        $this->actionDeletePeoplePositionCompanyBranch();

        $this->actionDeleteFiles();
        $this->actionDeleteInOutDocuments();
        $this->actionDeleteDocumentIn();
        $this->actionDeleteDocumentOut();

        $this->actionDeleteCompany();
        $this->actionDeletePeople();
        $this->actionDeleteUser();

        $this->actionDeleteBotMessage();
        $this->actionDeleteCertificateTemplates();
        $this->actionDeleteCharacteristicObject();
        $this->actionDeleteComplex();
        $this->actionDeleteEntry();
        $this->actionDeleteErrors();
        $this->actionDeleteForeignEventParticipants();
        $this->actionDeletePatchnotes();
        $this->actionDeletePosition();
        $this->actionDeleteProductUnion();
        $this->actionDeleteProjectTheme();
        $this->actionDeleteRussianNames();
    }
}