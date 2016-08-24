<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "clients".
 *
 * @property string $id
 * @property integer $active
 * @property integer $status
 * @property string $phone_number
 * @property integer $reg_id
 * @property string $image
 * @property integer $user_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property Addresses[] $addresses
 * @property BlacklistedClients[] $blacklistedClients
 * @property User $user
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
            [['active', 'status', 'reg_id', 'user_id'], 'required'],
            [['active', 'status', 'reg_id', 'user_id'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['phone_number', 'image'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'active' => 'Active',
            'status' => 'Status',
            'phone_number' => 'Phone Number',
            'reg_id' => 'Reg ID',
            'image' => 'Image',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'user_id' => 'User ID',
            'deleted_at' => 'Deleted At',
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
        return User::find()->where(['id' => $this->user_id])->where(['deleted_at' => null])->select(['id','email'])->asArray()->one();
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

    public function fields()
    {
        return [
            'id',
            'active',
            'status',
            'phone_number',
            'reg_id',
            'image',
            'user' => function(){
                return $this->getUser();
            }
        ];

    }
}
