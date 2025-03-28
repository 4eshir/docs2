<?php

namespace frontend\models\work\event;

use common\components\traits\ErrorTrait;
use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\models\scaffold\ForeignEvent;
use common\models\work\UserWork;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\general\PeopleStampRepository;
use frontend\models\work\dictionaries\CompanyWork;
use frontend\models\work\general\PeopleStampWork;
use frontend\models\work\general\PeopleWork;
use InvalidArgumentException;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * @property UserWork $creatorWork
 * @property UserWork $lastEditWork
 * @property CompanyWork $organizerWork
 */
class ForeignEventWork extends ForeignEvent
{
    use EventTrait, ErrorTrait;

    const EXPORT_TYPE = 1;
    const VIEW_TYPE = 2;

    public $actFiles;

    public static function fill(
        $name,
        $organizerId,
        $beginDate, $endDate,
        $city,
        $format, $level,
        $minister,
        $minAge, $maxAge,
        $keyWords,
        $orderParticipantId,
        $actFiles
    )
    {
        $entity = new static();
        $entity->name = $name;
        $entity->organizer_id = $organizerId;
        $entity->begin_date = $beginDate;
        $entity->end_date = $endDate;
        $entity->city = $city;
        $entity->format = $format;
        $entity->level = $level;
        $entity->minister = $minister;
        $entity->min_age = $minAge;
        $entity->max_age = $maxAge;
        $entity->key_words = $keyWords;
        $entity->order_participant_id = $orderParticipantId;
        $entity->actFiles = $actFiles;
        return $entity;
    }
    public function fillUpdate(
        $name,
        $organizerId,
        $beginDate, $endDate,
        $city,
        $format, $level,
        $minister,
        $minAge, $maxAge,
        $keyWords,
        $orderParticipantId,
        $actFiles
    )
    {
        $this->name = $name;
        $this->organizer_id = $organizerId;
        $this->begin_date = $beginDate;
        $this->end_date = $endDate;
        $this->city = $city;
        $this->format = $format;
        $this->level = $level;
        $this->minister = $minister;
        $this->min_age = $minAge;
        $this->max_age = $maxAge;
        $this->key_words = $keyWords;
        $this->order_participant_id = $orderParticipantId;
        $this->actFiles = $actFiles;
    }
    public function beforeValidate()
    {
        $this->begin_date = DateFormatter::format($this->begin_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        $this->end_date = DateFormatter::format($this->end_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        return parent::beforeValidate(); 
    }

    public function getTeachers($type = self::EXPORT_TYPE)
    {
        $acts = (Yii::createObject(ActParticipantRepository::class))->getByForeignEventIds([$this->id]);
        $teacherIds = array_merge(ArrayHelper::getColumn($acts, 'teacher_id'), ArrayHelper::getColumn($acts, 'teacher2_id'));
        $stamps = (Yii::createObject(PeopleStampRepository::class))->getStamps($teacherIds);
        $result = '';
        foreach ($stamps as $stamp) {
            /** @var PeopleStampWork $stamp */
            $result .= $stamp->getFIO(PeopleWork::FIO_SURNAME_INITIALS);
            if ($type == self::EXPORT_TYPE) {
                $result .= ' ';
            }
            else if ($type == self::VIEW_TYPE) {
                $result .= '<br>';
            }
        }

        return $result;
    }

    public function getFileLinks($filetype)
    {
        if (!array_key_exists($filetype, FilesHelper::getFileTypes())) {
            throw new InvalidArgumentException('Неизвестный тип файла');
        }
        $addPath = '';
        switch ($filetype) {
            case FilesHelper::TYPE_DOC:
                $addPath = FilesHelper::createAdditionalPath($this::tableName(), FilesHelper::TYPE_DOC);
                break;
        }
        return FilesHelper::createFileLinks($this, $filetype, $addPath);
    }

    public function getWinners()
    {
        return '';
    }

    public function getPrizes()
    {
        return '';
    }

    public function isTrip()
    {
        return !is_null($this->order_business_trip_id);
    }

    public function getCreatorWork()
    {
        return $this->hasOne(UserWork::class, ['id' => 'creator_id']);
    }

    public function getLastEditWork()
    {
        return $this->hasOne(UserWork::class, ['id' => 'last_edit_id']);
    }

    public function getOrganizerWork()
    {
        return $this->hasOne(CompanyWork::class, ['id' => 'organizer_id']);
    }
}