<?php

namespace common\helpers;

use common\components\dictionaries\base\ErrorDictionary;
use common\models\Error;

class ErrorAssociationHelper
{
    public static function getDocumentInErrorsList()
    {
        return [];
    }

    public static function getDocumentOutErrorsList()
    {
        return [];
    }

    public static function getOrderMainErrorsList()
    {
        return [
            ErrorDictionary::DOCUMENT_001,
            ErrorDictionary::DOCUMENT_003,
        ];
    }

    public static function getOrderStudyErrorsList()
    {

    }

    public static function getRegulationBaseErrorsList()
    {

    }

    public static function getRegulationEventErrorsList()
    {

    }

    public static function getEventErrorsList()
    {
        return [
            ErrorDictionary::ACHIEVE_008,
            ErrorDictionary::ACHIEVE_009,
            ErrorDictionary::ACHIEVE_010,
            ErrorDictionary::ACHIEVE_011,
            ErrorDictionary::ACHIEVE_012
        ];
    }

    public static function getForeignEventErrorsList()
    {
        return [
            ErrorDictionary::ACHIEVE_001,
            ErrorDictionary::ACHIEVE_002,
            ErrorDictionary::ACHIEVE_003,
            ErrorDictionary::ACHIEVE_005,
            ErrorDictionary::ACHIEVE_006,
            ErrorDictionary::ACHIEVE_007,
            ErrorDictionary::ACHIEVE_013
        ];
    }

    public static function getLocalResponsibilityErrorsList()
    {

    }

    public static function getTrainingGroupErrorsList()
    {
        return [
            ErrorDictionary::JOURNAL_001,
            ErrorDictionary::JOURNAL_002,
            ErrorDictionary::JOURNAL_003,
            ErrorDictionary::JOURNAL_004,
            ErrorDictionary::JOURNAL_005,
            ErrorDictionary::JOURNAL_006,
            ErrorDictionary::JOURNAL_007,
            ErrorDictionary::JOURNAL_008,
        ];
    }

    public static function getActParticipantErrorsList()
    {
        return [
            ErrorDictionary::ACHIEVE_004
        ];
    }
}