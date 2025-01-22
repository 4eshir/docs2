<?php

namespace frontend\forms\certificate;

use common\Model;
use common\repositories\educational\CertificateTemplatesRepository;
use common\repositories\educational\TrainingGroupRepository;
use frontend\models\work\CertificateTemplatesWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use Yii;

class CertificateForm extends Model
{
    public $id;
    /**
     * @var CertificateTemplatesWork[] $templates
     * @var TrainingGroupWork[] $groups
     */
    public array $templates;
    public array $groups;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->templates = (Yii::createObject(CertificateTemplatesRepository::class))->getAll();
        $this->groups = (Yii::createObject(TrainingGroupRepository::class))->getGroupsForCertificates();
    }
}