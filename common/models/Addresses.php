<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "addresses".
 *
 * @property string $id
 * @property string $address
 * @property string $client_id
 * @property string $area_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $is_default
 *
 * @property Areas $area
 * @property Clients $client
 * @property Orders[] $orders
 */
class Addresses extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'addresses';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['address', 'client_id', 'area_id'], 'required'],
            [['address'], 'string'],
            [['client_id', 'area_id', 'is_default'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => Areas::className(), 'targetAttribute' => ['area_id' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clients::className(), 'targetAttribute' => ['client_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'address' => 'Address',
            'client_id' => 'Client ID',
            'area_id' => 'Area ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
            'is_default' => 'Is Default',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArea()
    {
        return $this->hasOne(Areas::className(), ['id' => 'area_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Clients::className(), ['id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Orders::className(), ['address_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return AddressesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AddressesQuery(get_called_class());
    }
}