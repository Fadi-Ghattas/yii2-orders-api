<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "blacklisted_clients".
 *
 * @property string $id
 * @property string $reason
 * @property string $restaurant_id
 * @property string $client_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * 
 *
 * @property Clients $client
 * @property Restaurants $restaurant
 */
class BlacklistedClients extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'blacklisted_clients';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['reason', 'restaurant_id', 'client_id'], 'required'],
            [['reason'], 'string'],
            [['restaurant_id', 'client_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clients::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['restaurant_id'], 'exist', 'skipOnError' => true, 'targetClass' => Restaurants::className(), 'targetAttribute' => ['restaurant_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reason' => 'Reason',
            'restaurant_id' => 'Restaurant ID',
            'client_id' => 'Client ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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
    public function getRestaurant()
    {
        return $this->hasOne(Restaurants::className(), ['id' => 'restaurant_id']);
    }

    /**
     * @inheritdoc
     * @return BlacklistedClientsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BlacklistedClientsQuery(get_called_class());
    }
}
