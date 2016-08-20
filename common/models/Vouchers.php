<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "vouchers".
 *
 * @property string $id
 * @property string $code
 * @property string $name
 * @property string $value
 * @property string $minimum_order
 * @property string $start_date
 * @property string $expiry_date
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property Orders[] $orders
 */
class Vouchers extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vouchers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'name', 'value', 'start_date', 'expiry_date'], 'required'],
            [['value', 'minimum_order'], 'number'],
            [['start_date', 'expiry_date', 'created_at', 'updated_at'], 'safe'],
            [['code', 'name'], 'string', 'max' => 255],
            [['code'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'value' => 'Value',
            'minimum_order' => 'Minimum Order',
            'start_date' => 'Start Date',
            'expiry_date' => 'Expiry Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Orders::className(), ['voucher_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return VouchersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new VouchersQuery(get_called_class());
    }
}
