<?php

namespace console\controllers;

use common\models\LoginForm;
use common\models\work\general\UserWork;
use common\models\work\rac\PermissionFunctionWork;
use common\models\work\rac\PermissionTemplateFunctionWork;
use common\models\work\rac\PermissionTemplateWork;
use common\repositories\general\UserRepository;
use common\repositories\rac\PermissionFunctionRepository;
use common\repositories\rac\PermissionTemplateRepository;
use Yii;
use yii\console\Controller;

class FillController extends Controller
{
    private PermissionTemplateRepository $templateRepository;
    private PermissionFunctionRepository $functionRepository;
    private PermissionTemplateFunctionWork $templateFunctionRepository;

    public function __construct(
        $id,
        $module,
        PermissionTemplateRepository $templateRepository,
        PermissionFunctionRepository $functionRepository,
        PermissionTemplateFunctionWork $templateFunctionRepository,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->templateRepository = $templateRepository;
        $this->functionRepository = $functionRepository;
        $this->templateFunctionRepository = $templateFunctionRepository;
    }

    public function actionInitTemplates()
    {
        $tIds = [];
        $tIds[1] = $this->templateRepository->save(PermissionTemplateWork::fill('teacher'));
        $tIds[2] = $this->templateRepository->save(PermissionTemplateWork::fill('study_info'));
        $tIds[3] = $this->templateRepository->save(PermissionTemplateWork::fill('event_info'));
        $tIds[4] = $this->templateRepository->save(PermissionTemplateWork::fill('doc_info'));
        $tIds[5] = $this->templateRepository->save(PermissionTemplateWork::fill('material_info'));
        $tIds[6] = $this->templateRepository->save(PermissionTemplateWork::fill('branch_controller'));
        $tIds[7] = $this->templateRepository->save(PermissionTemplateWork::fill('super_controller'));
        $tIds[8] = $this->templateRepository->save(PermissionTemplateWork::fill('admin'));

        $fIds = [];
        $fIds[1] = $this->functionRepository->save(PermissionFunctionWork::fill('Добавление новых учебных групп'));
        $fIds[2] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр своих учебных групп'));
        $fIds[3] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр учебных групп своего отдела'));
        $fIds[4] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр всех учебных групп'));
        $fIds[5] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование своих учебных групп'));
        $fIds[6] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование учебных групп своего отдела'));
        $fIds[7] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование всех учебных групп'));
        $fIds[8] = $this->functionRepository->save(PermissionFunctionWork::fill('Удаление учебных групп своего отдела'));
        $fIds[9] = $this->functionRepository->save(PermissionFunctionWork::fill('Удаление всех учебных групп'));
        $fIds[10] = $this->functionRepository->save(PermissionFunctionWork::fill('Архивирование учебных групп своего отдела'));
        $fIds[11] = $this->functionRepository->save(PermissionFunctionWork::fill('Архивирование всех учебных групп'));
        $fIds[12] = $this->functionRepository->save(PermissionFunctionWork::fill('Прощение ошибок в образовательной деятельности'));
        $fIds[13] = $this->functionRepository->save(PermissionFunctionWork::fill('Прощение ошибок в основной деятельности'));

        $fIds[14] = $this->functionRepository->save(PermissionFunctionWork::fill('Удаление участников деятельности'));
        $fIds[15] = $this->functionRepository->save(PermissionFunctionWork::fill('Слияние участников деятельности'));
        $fIds[16] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование перечня персональных данных на обработку и распространение'));
        $fIds[17] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр образовательных программ'));
        $fIds[18] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование образовательных программ'));
        $fIds[19] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр приказов по образовательной деятельности'));
        $fIds[20] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование приказов по образовательной деятельности'));
        $fIds[21] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр приказов по основной деятельности'));
        $fIds[22] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование приказов по основной деятельности'));
        $fIds[23] = $this->functionRepository->save(PermissionFunctionWork::fill('Генерирование отчетов по запросу'));
        $fIds[24] = $this->functionRepository->save(PermissionFunctionWork::fill('Генерирование отчетов по формам'));
        $fIds[25] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр входящей документации'));
        $fIds[26] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование входящей документации'));
        $fIds[27] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр исходящей документации'));
        $fIds[28] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование исходящей документации'));
        $fIds[29] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр положений о мероприятиях'));
        $fIds[30] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование положений о мероприятиях'));
        $fIds[31] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр положений, инструкций и правил'));
        $fIds[32] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование положений, инструкций и правил'));
        $fIds[33] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр мероприятий'));
        $fIds[34] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование мероприятий'));
        $fIds[35] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр учета достижений в мероприятиях'));
        $fIds[36] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование учета достижений в мероприятиях'));
        $fIds[37] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр учета ответственности работников'));
        $fIds[38] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование учета ответственности работников'));

        $fIds[39] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр пользователей'));
        $fIds[40] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование пользователей'));
        $fIds[41] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование правил'));
        $fIds[42] = $this->functionRepository->save(PermissionFunctionWork::fill('Создание сертификатов'));
        $fIds[43] = $this->functionRepository->save(PermissionFunctionWork::fill('Удаление сертификатов'));
        $fIds[44] = $this->functionRepository->save(PermissionFunctionWork::fill('Доступ к основным административным функциям'));
        $fIds[45] = $this->functionRepository->save(PermissionFunctionWork::fill('Доступ к дополнительным административным функциям'));
        $fIds[46] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр справочника шаблонов сертификатов'));
        $fIds[47] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование справочника шаблонов сертификатов'));

        $fIds[48] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр мат. объектов и их объединений'));
        $fIds[49] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование мат. объектов и их объединений'));

        $fIds[50] = $this->functionRepository->save(PermissionFunctionWork::fill('Внутреннее перемещение мат. объектов по МОЛ'));

        $fIds[51] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр договоров и документов о поступлении мат. ценностей'));
        $fIds[52] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр договоров и документов о поступлении мат. ценностей'));
        $fIds[53] = $this->functionRepository->save(PermissionFunctionWork::fill('Удаление договоров и документов о поступлении мат. ценностей'));

        $fIds[54] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр справочника организаций'));
        $fIds[55] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование справочника организаций'));
        $fIds[56] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр справочника участников деятельности'));
        $fIds[57] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование справочника участников деятельности'));
        $fIds[58] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр справочника тематических направлений'));
        $fIds[59] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование справочника тематических направлений'));
        $fIds[60] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр справочника контейнеров'));
        $fIds[61] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование справочника контейнеров'));
        $fIds[62] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр справочников мат. ценностей'));
        $fIds[63] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование справочников мат. ценностей'));
        $fIds[64] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр справочника должностей'));
        $fIds[65] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование справочника должностей'));
        $fIds[66] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр справочника людей'));
        $fIds[67] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование справочника людей'));
        $fIds[68] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр справочника помещений'));
        $fIds[69] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование справочника помещений'));
        $fIds[70] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр справочника договоров'));
        $fIds[71] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование справочника договоров'));
        $fIds[72] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр справочника документов о поступлении'));
        $fIds[73] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование справочника документов о поступлении'));

        $this->templateFunctionRepository->save();
    }
}
