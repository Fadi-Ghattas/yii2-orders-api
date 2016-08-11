<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "orders".
 *
 * @property string $id
 * @property string $restaurant_id
 * @property string $client_id
 * @property string $reference_number
 * @property string $total
 * @property string $total_with_voucher
 * @property string $commission_amount
 * @property string $address_id
 * @property string $note
 * @property integer $status
 * @property string $voucher_id
 * @property string $payment_method_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property OrderItems[] $orderItems
 * @property Addresses $address
 * @property Clients $client
 * @property PaymentMethods $paymentMethod
 * @property Restaurants $restaurant
 * @property Vouchers $voucher
 */
class Orders extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['restaurant_id', 'client_id', 'reference_number', 'total', 'total_with_voucher', 'commission_amount', 'address_id', 'status', 'voucher_id', 'payment_method_id'], 'required'],
            [['restaurant_id', 'client_id', 'address_id', 'status', 'voucher_id', 'payment_method_id'], 'integer'],
            [['total', 'total_with_voucher', 'commission_amount'], 'number'],
            [['note'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['reference_number'], 'string', 'max' => 255],
            [['reference_number'], 'unique'],
            [['address_id'], 'exist', 'skipOnError' => true, 'targetClass' => Addresses::className(), 'targetAttribute' => ['address_id' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clients::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['payment_method_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentMethods::className(), 'targetAttribute' => ['payment_method_id' => 'id']],
            [['restaurant_id'], 'exist', 'skipOnError' => true, 'targetClass' => Restaurants::className(), 'targetAttribute' => ['restaurant_id' => 'id']],
            [['voucher_id'], 'exist', 'skipOnError' => true, 'targetClass' => Vouchers::className(), 'targetAttribute' => ['voucher_id' => 'id']],
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
            'client_id' => 'Client ID',
            'reference_number' => 'Reference Number',
            'total' => 'Total',
            'total_with_voucher' => 'Total With Voucher',
            'commission_amount' => 'Commission Amount',
            'address_id' => 'Address ID',
            'note' => 'Note',
            'status' => 'Status',
            'voucher_id' => 'Voucher ID',
            'payment_method_id' => 'Payment Method ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItems::className(), ['order_id' => 'id']);
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
    public function getClient()
    {
        return $this->hasOne(Clients::className(), ['id' => 'client_id']);
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
     * @return \yii\db\ActiveQuery
     */
    public function getVoucher()
    {
        return $this->hasOne(Vouchers::className(), ['id' => 'voucher_id']);
    }

    /**
     * @inheritdoc
     * @return OrdersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrdersQuery(get_called_class());
    }
}
