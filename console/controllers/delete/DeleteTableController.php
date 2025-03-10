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
    public function deletePeopleStamp()
    {
        Yii::$app->db->createCommand()->delete('people_stamp')->execute();
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
    public function actionDeleteEvent(){
        Yii::$app->db->createCommand()->delete('event')->execute();
    }
    public function actionDeleteEventBranch(){
        Yii::$app->db->createCommand()->delete('event_branch')->execute();
    }
    public function actionDeleteEventScope(){
        Yii::$app->db->createCommand()->delete('event_scope')->execute();
    }
    public function actionDeleteTrainingProgram(){
        Yii::$app->db->createCommand()->delete('training_program')->execute();
    }
    public function actionDeleteThematicPlan(){
        Yii::$app->db->createCommand()->delete('thematic_plan')->execute();
    }
    public function actionDeleteBranchProgram()
    {
        Yii::$app->db->createCommand()->delete('branch_program')->execute();
    }
    public function actionDeleteAuthorProgram()
    {
        Yii::$app->db->createCommand()->delete('author_program')->execute();
    }
    public function actionDeleteForeignEvent()
    {
        Yii::$app->db->createCommand()->delete('foreign_event')->execute();
    }
    public function actionDeleteDocumentOrder()
    {
        Yii::$app->db->createCommand()->delete('document_order')->execute();
    }
    public function actionDeleteDocumentOrderSupplement()
    {
        Yii::$app->db->createCommand()->delete('document_order_supplement')->execute();
    }
    public function actionDeleteAuditorium()
    {
        Yii::$app->db->createCommand()->delete('auditorium')->execute();
    }
    public function actionDeleteLocalResponsibility()
    {
        Yii::$app->db->createCommand()->delete('local_responsibility')->execute();
    }
    public function actionDeleteLegacyResponsible(){
        Yii::$app->db->createCommand()->delete('legacy_responsible')->execute();
    }
    public function actionDeleteTeamName()
    {
        Yii::$app->db->createCommand()->delete('team_name')->execute();
    }
    public function actionDeleteActParticipant()
    {
        Yii::$app->db->createCommand()->delete('act_participant')->execute();
    }
    public function actionDeleteSquadParticipant()
    {
        Yii::$app->db->createCommand()->delete('squad_participant')->execute();
    }
    public function actionDeleteActParticipantBranch()
    {
        Yii::$app->db->createCommand()->delete('act_participant_branch')->execute();
    }
    public function actionDeleteTrainingGroupParticipant()
    {
        Yii::$app->db->createCommand()->delete('training_group_participant')->execute();
    }
    public function actionDeleteGroupProjectThemes(){
        Yii::$app->db->createCommand()->delete('group_project_themes')->execute();
    }
    public function actionDeleteTrainingGroupLesson(){
        Yii::$app->db->createCommand()->delete('training_group_lesson')->execute();
    }
    public function actionDeleteTrainingGroupExpert(){
        Yii::$app->db->createCommand()->delete('training_group_expert')->execute();
    }
    public function actionDeleteTeacherGroup(){
        Yii::$app->db->createCommand()->delete('teacher_group')->execute();
    }
    public function actionDeleteTrainingGroup(){
        Yii::$app->db->createCommand()->delete('training_group')->execute();
    }
    public function actionDeleteVisit()
    {
        Yii::$app->db->createCommand()->delete('visit')->execute();
    }
    public function actionDeleteLessonTheme()
    {
        Yii::$app->db->createCommand()->delete('lesson_theme')->execute();
    }
    public function actionDeleteParticipantAchievement(){
        Yii::$app->db->createCommand()->delete('participant_achievement')->execute();
    }
    public function actionDeleteCertificate()
    {
        Yii::$app->db->createCommand()->delete('certificate')->execute();
    }
    public function actionDeleteInitCopy()
    {
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
    public function actionDeleteDocumentOrderCopy()
    {
        $this->actionDeleteDocumentOrder();
        $this->actionDeleteDocumentOrderSupplement();
    }
    public function actionDeleteDocumentInOutCopy()
    {
        $this->actionDeleteFiles();
        $this->actionDeleteInOutDocuments();
        $this->actionDeleteDocumentIn();
        $this->actionDeleteDocumentOut();
        $this->deletePeopleStamp();
        $this->actionDeleteUser();
        $this->actionDeletePeople();
        $this->actionDeleteCompany();
    }
    public function actionDeleteEventCopy()
    {
        $this->actionDeleteEventScope();
        $this->actionDeleteEventBranch();
        $this->actionDeleteEvent();
    }
    public function actionDeleteTrainingProgramCopy()
    {
        $this->actionDeleteAuthorProgram();
        $this->actionDeleteBranchProgram();
        $this->actionDeleteThematicPlan();
        $this->actionDeleteTrainingProgram();
    }
    public function actionDeleteLocalResponsibilityCopy()
    {

        $this->actionDeleteLocalResponsibility();
        $this->actionDeleteLegacyResponsible();
        $this->actionDeleteAuditorium();
    }
    public function actionDeleteActCopy()
    {
        $this->actionDeleteActParticipantBranch();
        $this->actionDeleteSquadParticipant();
        $this->actionDeleteActParticipant();
        $this->actionDeleteTeamName();
    }
    public function actionDeleteTrainingGroupCopy()
    {
        $this->actionDeleteTrainingGroupParticipant();
        $this->actionDeleteGroupProjectThemes();
        $this->actionDeleteTrainingGroupLesson();
        $this->actionDeleteTrainingGroupExpert();
        $this->actionDeleteTeacherGroup();
        $this->actionDeleteTrainingGroup();
    }
    public function actionDeleteAll()
    {
        $this->actionDeleteActCopy();
        $this->actionDeleteForeignEvent();
        $this->actionDeleteLocalResponsibilityCopy();
        $this->actionDeleteDocumentOrderCopy();
        $this->actionDeleteTrainingProgramCopy();
        $this->actionDeleteEventCopy();
        $this->actionDeletePersonalData();
        $this->actionDeleteRegulation();
        $this->actionDeletePeoplePositionCompanyBranch();
        $this->actionDeleteDocumentInOutCopy();
        $this->actionDeleteInitCopy();
        $this->actionDeleteTrainingGroupCopy();
        $this->actionDeleteVisit();
        $this->actionDeleteLessonTheme();
        $this->actionDeleteParticipantAchievement();
        $this->actionDeleteCertificate();
    }
}