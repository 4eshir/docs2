<?php

namespace frontend\forms\event;

use app\models\work\team\ActParticipantWork;
use common\events\EventTrait;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\act_participant\SquadParticipantRepository;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class EventParticipantForm extends Model
{
    use EventTrait;

    private ActParticipantRepository $actParticipantRepository;
    private SquadParticipantRepository $squadParticipantRepository;

    public ActParticipantWork $actParticipant;

    public array $branches;
    public $fileMaterial;
    public $fileMaterialTable;

    public function __construct(
        $actParticipantId,
        ActParticipantRepository $actParticipantRepository = null,
        SquadParticipantRepository $squadParticipantRepository = null,
        $config = [])
    {
        parent::__construct($config);
        if (!$actParticipantRepository) {
            $actParticipantRepository = Yii::createObject(ActParticipantRepository::class);
        }
        if (!$squadParticipantRepository) {
            $squadParticipantRepository = Yii::createObject(SquadParticipantRepository::class);
        }
        $this->actParticipantRepository = $actParticipantRepository;
        $this->squadParticipantRepository = $squadParticipantRepository;

        $this->actParticipant = $this->actParticipantRepository->get($actParticipantId);
        $this->branches = ArrayHelper::getColumn(
            $this->actParticipantRepository->getParticipantBranches($actParticipantId), 'branches'
        );
    }

    public function fillTable()
    {
        $materials = $this->actParticipant->getFileLinks(FilesHelper::TYPE_MATERIAL);
        return HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($materials, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($materials), $this->actParticipant->id), 'fileId' => ArrayHelper::getColumn($materials, 'id')])
            ]
        );
    }

    public function save()
    {
        $this->actParticipantRepository->save($this->actParticipant);
    }
}