<?php

namespace common\helpers;

use common\components\dictionaries\base\ErrorDictionary;

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
            ErrorDictionary::DOCUMENT_002,
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

    }

    public static function getForeignEventErrorsList()
    {

    }

    public static function getLocalResponsibilityErrorsList()
    {

    }

    public static function getTrainingGroupErrorsList()
    {

    }
}