<?php

namespace common\components\dictionaries;

use common\components\dictionaries\base\BaseDictionary;
use common\models\scaffold\Auditorium;
use common\models\scaffold\AuthorProgram;
use common\models\scaffold\BotMessage;
use common\models\scaffold\BranchProgram;
use common\models\scaffold\CertificateTemplates;
use common\models\scaffold\CharacteristicObject;
use common\models\scaffold\Company;
use common\models\scaffold\Complex;
use common\models\scaffold\DocumentIn;
use common\models\scaffold\DocumentOrder;
use common\models\scaffold\DocumentOut;
use common\models\scaffold\Entry;
use common\models\scaffold\Errors;
use common\models\scaffold\Event;
use common\models\scaffold\EventBranch;
use common\models\scaffold\EventExternal;
use common\models\scaffold\EventScope;
use common\models\scaffold\Expire;
use common\models\scaffold\Files;
use common\models\scaffold\ForeignEventParticipants;
use common\models\scaffold\InOutDocuments;
use common\models\scaffold\LegacyResponsible;
use common\models\scaffold\LocalResponsibility;
use common\models\scaffold\ObjectStates;
use common\models\scaffold\Patchnotes;
use common\models\scaffold\People;
use common\models\scaffold\PeoplePositionCompanyBranch;
use common\models\scaffold\PeopleStamp;
use common\models\scaffold\PermissionFunction;
use common\models\scaffold\PermissionTemplate;
use common\models\scaffold\PermissionTemplateFunction;
use common\models\scaffold\PermissionToken;
use common\models\scaffold\PersonalDataParticipant;
use common\models\scaffold\Position;
use common\models\scaffold\ProductUnion;
use common\models\scaffold\ProjectTheme;
use common\models\scaffold\Regulation;
use common\models\scaffold\RussianNames;
use common\models\scaffold\ThematicPlan;
use common\models\scaffold\TrainingProgram;
use common\models\User;
use frontend\models\work\general\PeopleWork;
use frontend\models\work\general\RussianNamesWork;
use frontend\models\work\rac\UserPermissionFunctionWork;

class TableDictionary extends BaseDictionary
{
    public function __construct()
    {
        parent::__construct();
        $this->list = [
            Auditorium::tableName() => 'Помещения',
            AuthorProgram::tableName() => 'Авторы учебных программ',
            BotMessage::tableName() => 'Сообщения бота',
            BranchProgram::tableName() => 'Отделы реализации учебных программ',
            CertificateTemplates::tableName() => 'Шаблоны сертификатов',
            CharacteristicObject::tableName() => 'Характеристики объектов',
            Company::tableName() => 'Организации',
            Complex::tableName() => 'Комплексы объектов',
            DocumentIn::tableName() => 'Входящая документация',
            DocumentOrder::tableName() => 'Приказы',
            DocumentOut::tableName() => 'Исходящая документация',
            Entry::tableName() => 'Документы о поступлении',
            Errors::tableName() => 'Ошибки',
            Event::tableName() => 'Мероприятия',
            EventBranch::tableName() => 'Отделы-мероприятия',
            EventExternal::tableName() => 'Внешние мероприятия',
            EventScope::tableName() => 'Направленности мероприятий',
            Expire::tableName() => 'Ограничения или изменения документов',
            Files::tableName() => 'Файлы',
            ForeignEventParticipants::tableName() => 'Участники деятельности',
            InOutDocuments::tableName() => 'Ответы на документы',
            LegacyResponsible::tableName() => 'История передачи ответственности',
            LocalResponsibility::tableName() => '',
            ObjectStates::tableName() => 'Состояния объектов',
            Patchnotes::tableName() => 'Патчноуты',
            People::tableName() => 'Люди',
            PeoplePositionCompanyBranch::tableName() => 'Организации-должности-люди',
            PeopleStamp::tableName() => 'Копии людей',
            PermissionFunction::tableName() => 'Функции Rule-Based Access Model',
            PermissionTemplate::tableName() => 'Шаблоны Rule-Based Access Model',
            PermissionTemplateFunction::tableName() => 'Функции для шаблонов Rule-Based Access Model',
            PermissionToken::tableName() => 'Временные токены доступа для Rule-Based Access Model',
            PersonalDataParticipant::tableName() => 'Ограничения разглашения персональных данных',
            Position::tableName() => 'Должности',
            ProductUnion::tableName() => 'Объединения объектов',
            ProjectTheme::tableName() => 'Темы проектов',
            Regulation::tableName() => 'Положения',
            RussianNames::tableName() => 'Русские имена',
            ThematicPlan::tableName() => 'Тематический план',
            TrainingProgram::tableName() => 'Учебные программы',
            User::tableName() => 'Пользователи',
            UserPermissionFunctionWork::tableName() => 'Пользователи-функции Rule-Based Access Model',
        ];
    }

    public function customSort()
    {
        return [

        ];
    }
}