<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "payment_method_restaurant".
 *
 * @property string $id
 * @property string $restaurant_id
 * @property string $payment_method_id
 *
 * @property PaymentMethods $paymentMethod
 * @property Restaurants $restaurant
 */
class PaymentMethodRestaurant extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment_method_restaurant';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['restaurant_id', 'payment_method_id'], 'required'],
            [['restaurant_id', 'payment_method_id'], 'integer'],
            [['payment_method_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentMethods::className(), 'targetAttribute' => ['payment_method_id' => 'id']],
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
            'restaurant_id' => 'Restaurant ID',
            'payment_method_id' => 'Payment Method ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentMethod()
    {
        return $this->hasOne(PaymentMethods::className(), ['id' => 'payment_method_id']);
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
     * @return PaymentMethodRestaurantQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PaymentMethodRestaurantQuery(get_called_class());
    }
}
