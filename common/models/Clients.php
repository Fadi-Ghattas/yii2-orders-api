<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "clients".
 *
 * @property string $id
 * @property string $address_id
 * @property integer $active
 * @property integer $status
 * @property string $phone_number
 * @property integer $reg_id
 * @property string $image
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $user_id
 *
 * @property Addresses[] $addresses
 * @property BlacklistedClients[] $blacklistedClients
 * @property User $user
 * @property Addresses $address
 * @property FavoriteRestaurants[] $favoriteRestaurants
 * @property Feedbacks[] $feedbacks
 * @property Orders[] $orders
 * @property ResetPasswords[] $resetPasswords
 * @property Reviews[] $reviews
 */
class Clients extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'clients';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['address_id', 'active', 'status', 'phone_number', 'reg_id', 'user_id'], 'required'],
            [['address_id', 'active', 'status', 'reg_id', 'user_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['phone_number', 'image'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['address_id'], 'exist', 'skipOnError' => true, 'targetClass' => Addresses::className(), 'targetAttribute' => ['address_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'address_id' => 'Address ID',
            'active' => 'Active',
            'status' => 'Status',
            'phone_number' => 'Phone Number',
            'reg_id' => 'Reg ID',
            'image' => 'Image',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'user_id' => 'User ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddresses()
    {
        return $this->hasMany(Addresses::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBlacklistedClients()
    {
        return $this->hasMany(BlacklistedClients::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne(Addresses::className(), ['id' => 'address_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFavoriteRestaurants()
    {
        return $this->hasMany(FavoriteRestaurants::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeedbacks()
    {
        return $this->hasMany(Feedbacks::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Orders::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResetPasswords()
    {
        return $this->hasMany(ResetPasswords::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReviews()
    {
        return $this->hasMany(Reviews::className(), ['client_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return ClientsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ClientsQuery(get_called_class());
    }
}
