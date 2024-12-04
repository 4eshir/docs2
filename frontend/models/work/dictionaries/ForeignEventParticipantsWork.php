<?php

namespace frontend\models\work\dictionaries;

use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\helpers\html\HtmlBuilder;
use common\models\scaffold\ForeignEventParticipants;
use common\models\scaffold\PersonalDataParticipant;
use frontend\models\work\general\PeopleWork;
use InvalidArgumentException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class ForeignEventParticipantsWork extends ForeignEventParticipants
{
    use EventTrait;

    /**
     * DROP_CORRECT_HARD - сброс флагов true и guaranteed_true
     * DROP_CORRECT_SOFT - сброс флага true
     */
    const DROP_CORRECT_HARD = 0;
    const DROP_CORRECT_SOFT = 1;

    const FIO_FULL = 1;
    const FIO_SURNAME_INITIALS = 2;

    // Список запрещенных к разглашению ПД
    public $pd;

    /**
     * Сведения о разглашении ПД @see PersonalDataParticipantWork
     * @var mixed|null
     */
    public $personalData;

    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => function() {
                    return date('Y-m-d H:i:s');
                },
            ],
        ];
    }

    public static function fill(
        $firstname,
        $surname,
        $birthdate,
        $email,
        $sex,
        $patronymic = ''
    )
    {
        $entity = new static();
        $entity->firstname = $firstname;
        $entity->surname = $surname;
        $entity->birthdate = $birthdate;
        $entity->email = $email;
        $entity->sex = $sex;
        $entity->patronymic = $patronymic;

        return $entity;
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'pd' => 'Запретить разглашение персональных данных',
        ]);
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['pd'], 'safe'],
        ]);
    }

    public function getFIO($type)
    {
        switch ($type) {
            case self::FIO_FULL:
                return $this->getFullFio();
            case self::FIO_SURNAME_INITIALS:
                return $this->getSurnameInitials();
            default:
                throw new InvalidArgumentException('Неизвестный тип вывода ФИО');
        }
    }

    public function getFullFio()
    {
        return "$this->surname $this->firstname $this->patronymic";
    }

    public function getSurnameInitials()
    {
        return $this->surname
            . ' ' . mb_substr($this->firstname, 0, 1)
            . '. ' . ($this->patronymic ? mb_substr($this->patronymic, 0, 1) . '.' : '');
    }

    public function fillPersonalDataRestrict(array $data)
    {
        $this->pd = [];
        if (count($data) > 0) {
            foreach ($data as $one) {
                /** @var PersonalDataParticipantWork $one */
                if ($one->isRestrict()) {
                    $this->pd[] = $one->personal_data;
                }
            }
        }
    }

    public function isTrueAnyway()
    {
        return $this->id === null || $this->is_true === 1 || $this->guaranteed_true === 1;
    }

    public function isGuaranteedTrue()
    {
        return $this->guaranteed_true === 1;
    }

    public function getSexString()
    {
        switch ($this->sex) {
            case 0:
                return 'Мужской';
            case 1:
                return 'Женский';
            default:
                return 'Другое';
        }
    }

    public function createRawPersonalData()
    {
        return HtmlBuilder::createPersonalDataTable($this->pd);
    }

    public function setNotTrue($type = self::DROP_CORRECT_HARD)
    {
        if (!$this->isGuaranteedTrue() && $type !== self::DROP_CORRECT_HARD) {
            $this->is_true = 0;
            if (self::DROP_CORRECT_HARD) {
                $this->guaranteed_true = 0;
            }
        }
    }

    public function beforeValidate()
    {
        $this->birthdate = DateFormatter::format($this->birthdate, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        return parent::beforeValidate(); 
    }
}
