<?php

namespace console\controllers;

use common\models\LoginForm;
use common\models\work\general\CompanyWork;
use common\models\work\general\UserWork;
use common\models\work\rac\PermissionFunctionWork;
use common\models\work\rac\PermissionTemplateFunctionWork;
use common\models\work\rac\PermissionTemplateWork;
use common\repositories\general\CompanyRepository;
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
    private CompanyRepository $companyRepository;

    public function __construct(
        $id,
        $module,
        PermissionTemplateRepository $templateRepository,
        PermissionFunctionRepository $functionRepository,
        PermissionTemplateFunctionWork $templateFunctionRepository,
        CompanyRepository $companyRepository,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->templateRepository = $templateRepository;
        $this->functionRepository = $functionRepository;
        $this->templateFunctionRepository = $templateFunctionRepository;
        $this->companyRepository = $companyRepository;
    }

    public function actionInit()
    {
        $this->companyRepository->save(
            $this->companyRepository->fastCreateWithId(
                Yii::$app->params['mainCompanyId'],
                'ГАОУ АО ДО "Региональный школьный технопарк',
                'РШТ',
                0)
        );
    }

    public function actionDropPermissions()
    {
        PermissionTemplateFunctionWork::deleteAll();
        PermissionFunctionWork::deleteAll();
        PermissionTemplateWork::deleteAll();
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
        $fIds[1] = $this->functionRepository->save(PermissionFunctionWork::fill('Добавление новых учебных групп', 'add_group'));
        $fIds[2] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр своих учебных групп', 'view_self_groups'));
        $fIds[3] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр учебных групп своего отдела', 'view_branch_groups'));
        $fIds[4] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр всех учебных групп', 'view_all_groups'));
        $fIds[5] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование своих учебных групп', 'edit_self_groups'));
        $fIds[6] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование учебных групп своего отдела', 'edit_branch_groups'));
        $fIds[7] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование всех учебных групп', 'edit_all_groups'));
        $fIds[8] = $this->functionRepository->save(PermissionFunctionWork::fill('Удаление учебных групп своего отдела', 'delete_branch_groups'));
        $fIds[9] = $this->functionRepository->save(PermissionFunctionWork::fill('Удаление всех учебных групп', 'delete_all_groups'));
        $fIds[10] = $this->functionRepository->save(PermissionFunctionWork::fill('Архивирование учебных групп своего отдела', 'archive_branch_groups'));
        $fIds[11] = $this->functionRepository->save(PermissionFunctionWork::fill('Архивирование всех учебных групп', 'archive_all_groups'));
        $fIds[12] = $this->functionRepository->save(PermissionFunctionWork::fill('Прощение ошибок в образовательной деятельности', 'forgive_study_errors'));
        $fIds[13] = $this->functionRepository->save(PermissionFunctionWork::fill('Прощение ошибок в основной деятельности', 'forgive_base_errors'));
        $fIds[14] = $this->functionRepository->save(PermissionFunctionWork::fill('Удаление участников деятельности', 'delete_participants'));
        $fIds[15] = $this->functionRepository->save(PermissionFunctionWork::fill('Слияние участников деятельности', 'merge_participants'));
        $fIds[16] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование перечня персональных данных на обработку и распространение', 'edit_personal_data'));
        $fIds[17] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр образовательных программ', 'view_training_programs'));
        $fIds[18] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование образовательных программ', 'edit_training_programs'));
        $fIds[19] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр приказов по образовательной деятельности', 'view_study_orders'));
        $fIds[20] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование приказов по образовательной деятельности', 'edit_study_orders'));
        $fIds[21] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр приказов по основной деятельности', 'view_base_orders'));
        $fIds[22] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование приказов по основной деятельности', 'edit_base_orders'));
        $fIds[23] = $this->functionRepository->save(PermissionFunctionWork::fill('Генерирование отчетов по запросу', 'gen_report_query'));
        $fIds[24] = $this->functionRepository->save(PermissionFunctionWork::fill('Генерирование отчетов по формам', 'gen_report_forms'));
        $fIds[25] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр входящей документации', 'view_doc_in'));
        $fIds[26] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование входящей документации', 'edit_doc_in'));
        $fIds[27] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр исходящей документации', 'view_doc_out'));
        $fIds[28] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование исходящей документации', 'edit_doc_out'));
        $fIds[29] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр положений о мероприятиях', 'view_event_regulations'));
        $fIds[30] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование положений о мероприятиях', 'edit_event_regulations'));
        $fIds[31] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр положений, инструкций и правил', 'view_base_regulations'));
        $fIds[32] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование положений, инструкций и правил', 'edit_base_regulations'));
        $fIds[33] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр мероприятий', 'view_events'));
        $fIds[34] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование мероприятий', 'edit_events'));
        $fIds[35] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр учета достижений в мероприятиях', 'view_foreign_events'));
        $fIds[36] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование учета достижений в мероприятиях', 'edit_foreign_events'));
        $fIds[37] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр учета ответственности работников', 'view_local_resp'));
        $fIds[38] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование учета ответственности работников', 'edit_local_resp'));
        $fIds[39] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр пользователей', 'view_users'));
        $fIds[40] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование пользователей', 'edit_users'));
        $fIds[41] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование правил', 'edit_permissions'));
        $fIds[42] = $this->functionRepository->save(PermissionFunctionWork::fill('Создание сертификатов', 'create_certificates'));
        $fIds[43] = $this->functionRepository->save(PermissionFunctionWork::fill('Удаление сертификатов', 'delete_certificates'));
        $fIds[44] = $this->functionRepository->save(PermissionFunctionWork::fill('Доступ к основным административным функциям', 'allow_base_admin'));
        $fIds[45] = $this->functionRepository->save(PermissionFunctionWork::fill('Доступ к дополнительным административным функциям', 'allow_extended_admin'));
        $fIds[46] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр справочника шаблонов сертификатов', 'view_certificate_template'));
        $fIds[47] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование справочника шаблонов сертификатов', 'edit_certificate_template'));
        $fIds[48] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр мат. объектов и их объединений', 'view_material_obj'));
        $fIds[49] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование мат. объектов и их объединений', 'edit_material_obj'));
        $fIds[50] = $this->functionRepository->save(PermissionFunctionWork::fill('Внутреннее перемещение мат. объектов по МОЛ', 'move_material_obj'));
        $fIds[51] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр справочника организаций', 'view_companies'));
        $fIds[52] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование справочника организаций', 'edit_companies'));
        $fIds[53] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр справочника участников деятельности', 'view_participants'));
        $fIds[54] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование справочника участников деятельности', 'edit_participants'));
        $fIds[55] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр справочника тематических направлений', 'view_thematic_direct'));
        $fIds[56] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование справочника тематических направлений', 'edit_thematic_direct'));
        $fIds[57] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр справочника контейнеров', 'view_containers'));
        $fIds[58] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование справочника контейнеров', 'edit_containers'));
        $fIds[59] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр справочника должностей', 'view_positions'));
        $fIds[60] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование справочника должностей', 'edit_positions'));
        $fIds[61] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр справочника людей', 'view_peoples'));
        $fIds[62] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование справочника людей', 'edit_peoples'));
        $fIds[63] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр справочника помещений', 'view_auditoriums'));
        $fIds[64] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование справочника помещений', 'edit_auditoriums'));
        $fIds[65] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр справочника договоров', 'view_contracts'));
        $fIds[66] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование справочника договоров', 'edit_contracts'));
        $fIds[67] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр справочника документов о поступлении', 'view_invoices'));
        $fIds[68] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование справочника документов о поступлении', 'edit_invoices'));

        $this->templateFunctionRepository->save();
    }
}
