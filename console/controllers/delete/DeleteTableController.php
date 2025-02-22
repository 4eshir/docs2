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
    public function actionDeleteAll()
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
}