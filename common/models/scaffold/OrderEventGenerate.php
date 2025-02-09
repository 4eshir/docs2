<?php

namespace common\models\scaffold;
use Yii;

/**
 * This is the model class for table "order_event_generate".
 *
 * @property int $id
 * @property int $order_id
 * @property int|null $purpose
 * @property int|null $doc_event
 * @property int|null $resp_people_info_id
 * @property int|null $extra_resp_insert_id
 * @property int|null $time_provision_day
 * @property int|null $time_insert_day
 * @property int|null $extra_resp_method_id
 * @property int|null $extra_resp_info_stuff_id
 *
 * @property PeopleStamp $extraRespInfoStuff
 * @property PeopleStamp $extraRespInsert
 * @property PeopleStamp $extraRespMethod
 * @property DocumentOrder $order
 * @property PeopleStamp $respPeopleInfo
 */
class OrderEventGenerate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_event_generate';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id'], 'required'],
            [['order_id', 'purpose', 'doc_event', 'resp_people_info_id', 'extra_resp_insert_id', 'time_provision_day', 'time_insert_day', 'extra_resp_method_id', 'extra_resp_info_stuff_id'], 'integer'],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::class, 'targetAttribute' => ['order_id' => 'id']],
            [['extra_resp_info_stuff_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleStamp::class, 'targetAttribute' => ['extra_resp_info_stuff_id' => 'id']],
            [['extra_resp_insert_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleStamp::class, 'targetAttribute' => ['extra_resp_insert_id' => 'id']],
            [['extra_resp_method_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleStamp::class, 'targetAttribute' => ['extra_resp_method_id' => 'id']],
            [['resp_people_info_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleStamp::class, 'targetAttribute' => ['resp_people_info_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'purpose' => 'Purpose',
            'doc_event' => 'Doc Event',
            'resp_people_info_id' => 'Resp People Info ID',
            'extra_resp_insert_id' => 'Extra Resp Insert ID',
            'time_provision_day' => 'Time Provision Day',
            'time_insert_day' => 'Time Insert Day',
            'extra_resp_method_id' => 'Extra Resp Method ID',
            'extra_resp_info_stuff_id' => 'Extra Resp Info Stuff ID',
        ];
    }

    /**
     * Gets query for [[ExtraRespInfoStuff]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExtraRespInfoStuff()
    {
        return $this->hasOne(PeopleStamp::class, ['id' => 'extra_resp_info_stuff_id']);
    }

    /**
     * Gets query for [[ExtraRespInsert]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExtraRespInsert()
    {
        return $this->hasOne(PeopleStamp::class, ['id' => 'extra_resp_insert_id']);
    }

    /**
     * Gets query for [[ExtraRespMethod]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExtraRespMethod()
    {
        return $this->hasOne(PeopleStamp::class, ['id' => 'extra_resp_method_id']);
    }

    /**
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(DocumentOrder::class, ['id' => 'order_id']);
    }

    /**
     * Gets query for [[RespPeopleInfo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRespPeopleInfo()
    {
        return $this->hasOne(PeopleStamp::class, ['id' => 'resp_people_info_id']);
    }
}