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
    public function getTeamsWithParticipants($data, $model){
        $index = 0;
        $inputArray = $data;
        $array = [];
        $participants = [];
        $focuses = [];
        $branches = [];
        $teachers = [];
        $teachers2 = [];
        $nominations = [];
        $formRealization = [];
        foreach($inputArray as $element) {
            if($element['participant_id'] != NULL && $element['participant_id'] != ""){
                if($element['participant_id'][0] != "") {
                    array_push($participants, $element['participant_id']);
                }
            }
            if($element['focus'] != NULL && $element['focus'] != "") {
                if($element['focus'][0] != "") {
                    array_push($focuses, $element['focus']);
                }
            }
            if($element['branch'] != NULL && $element['branch'] != "") {
                if($element['branch'][0] != "") {
                    array_push($branches, $element['branch']);
                }
            }
            if($element['teacher_id'] != NULL && $element['teacher_id'] != "") {
                if($element['teacher_id'][0] != "") {
                    array_push($teachers, $element['teacher_id']);
                }
            }
            if($element['teacher2_id'] != NULL && $element['teacher2_id'] != "") {
                if($element['teacher2_id'][0] != "") {
                    array_push($teachers2, $element['teacher2_id']);
                }
            }
            if($element['formRealization'] != NULL && $element['formRealization'] != "") {
                if($element['formRealization'][0] != "") {
                    array_push($formRealization, $element['formRealization']);
                }
            }
            if($element['nominationList'] != NULL && $element['nominationList'] != "") {
                if($element['nominationList'][0] != "") {
                    array_push($nominations, $element['nominationList']);
                }
            }
            if($element['teamList'] != NULL && $element['teamList'] != "" ) {
                if ($element['teamList'][0] != "" && $participants != NULL) {
                    $item = [
                        'team' => $element['teamList'],
                        'participants' => $participants,
                        'focus' => $focuses,
                        'branches' => $branches,
                        'teachers' => $teachers,
                        'teachers2' => $teachers2,
                        'formRealization' => $formRealization,
                        'nominations' => $nominations,
                        'files' => UploadedFile::getInstances($model, 'part['. $index . ']')
                    ];
                    array_push($array, $item);

                }
                $participants = [];
                $focuses = [];
                $branches = [];
                $teachers = [];
                $teachers2 = [];
                $nominations = [];
                $formRealization = [];
                $index++;
            }
        }
        return $array;
    }
    public function getPersonalParticipants($data, $model)
    {
        $index = 0;
        $inputArray = $data;
        $array = [];
        $participants = [];
        $focuses = [];
        $branches = [];
        $teachers = [];
        $teachers2 = [];
        $formRealization = [];
        foreach($inputArray as $element) {
            if($element['participant_id'] != NULL && $element['participant_id'] != ""){
                if($element['participant_id'][0] != "") {
                    array_push($participants, $element['participant_id']);
                }
            }
            if($element['focus'] != NULL && $element['focus'] != "") {
                if($element['focus'][0] != "") {
                    array_push($focuses, $element['focus']);
                }
            }
            if($element['branch'] != NULL && $element['branch'] != "") {
                if($element['branch'][0] != "") {
                    array_push($branches, $element['branch']);
                }
            }
            if($element['teacher_id'] != NULL && $element['teacher_id'] != "") {
                if($element['teacher_id'][0] != "") {
                    array_push($teachers, $element['teacher_id']);
                }
            }
            if($element['teacher2_id'] != NULL && $element['teacher2_id'] != "") {
                if($element['teacher2_id'][0] != "") {
                    array_push($teachers2, $element['teacher2_id']);
                }
            }
            if($element['formRealization'] != NULL && $element['formRealization'] != "") {
                if($element['formRealization'][0] != "") {
                    array_push($formRealization, $element['formRealization']);
                }
            }
            if($element['nominationList'] != NULL && $element['nominationList'] != "" ) {
                if($element['nominationList'][0] != "" && $participants != NULL) {
                    $item = [
                        'participants' => $participants,
                        'focus' => $focuses,
                        'branches' => $branches,
                        'teachers' => $teachers,
                        'teachers2' => $teachers2,
                        'formRealization' => $formRealization,
                        'nominations' => $element['nominationList'],
                        'files' => UploadedFile::getInstances($model, 'personal['. $index . ']')
                    ];
                    array_push($array, $item);

                }
                $participants = [];
                $focuses = [];
                $branches = [];
                $teachers = [];
                $teachers2 = [];
                $formRealization = [];
                $index++;
            }
        }
        return $array;
    }
}