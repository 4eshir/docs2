<?php

namespace app\services\event;

use frontend\forms\OrderEventForm;
use yii\web\UploadedFile;

class OrderEventFormService
{
    public function getFilesInstances(OrderEventForm $model)
    {
        $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
        $model->docFiles = UploadedFile::getInstances($model, 'docFiles');
        $model->actFiles = UploadedFile::getInstances($model, 'actFiles');
    }
    public function getTeamsWithParticipants($post){
        $inputArray = $post['OrderEventForm']['part'];
        $array = [];
        $participants = [];
        foreach($inputArray as $element) {
            if($element['participant_id'] != NULL && $element['participant_id'] != ""){
                array_push($participants, $element['participant_id']);
            }
            if($element['teamList'] != NULL && $element['teamList'] != "" && $participants != NULL) {
                $item = ['team' => $element['teamList'], 'participants' => $participants];
                array_push($array, $item);
                $participants = [];
            }
        }
        return $array;
    }
}