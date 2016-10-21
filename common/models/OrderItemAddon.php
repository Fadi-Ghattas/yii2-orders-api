<?php

namespace common\models;


use Yii;
use common\helpers\Helpers;

/**
 * This is the model class for table "order_item_addon".
 *
 * @property string $order_item_id
 * @property string $addon_id
 * @property string $price
 * @property integer $quantity
 *
 * @property Addons $addon
 * @property OrderItems $orderItem
 */
class OrderItemAddon extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_item_addon';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_item_id', 'addon_id'], 'required'],
            [['order_item_id', 'addon_id', 'quantity'], 'integer'],
            [['quantity'], 'compare', 'compareValue' => 0, 'operator' => '>'],
            [['price'], 'number'],
            [['addon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Addons::className(), 'targetAttribute' => ['addon_id' => 'id']],
            [['order_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrderItems::className(), 'targetAttribute' => ['order_item_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_item_id' => 'Order Item ID',
            'addon_id' => 'Addon ID',
            'price' => 'Price',
            'quantity' => 'Quantity',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddon()
    {
        return $this->hasOne(Addons::className(), ['id' => 'addon_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderItem()
    {
        return $this->hasOne(OrderItems::className(), ['id' => 'order_item_id']);
    }

    /**
     * @inheritdoc
     * @return OrderItemAddonQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrderItemAddonQuery(get_called_class());
    }

    public function afterValidate()
    {
        if ($this->hasErrors()) {
            return Helpers::HttpException(422, 'validation failed', ['error' => $this->errors]);
        }
    }

    public function fields()
    {
        return [
            'id' => function () {
                return (int)$this->addon->id;
            },
            'name' => function () {
                return (string)$this->addon->name;
            },
            'description' => function () {
                return (string)$this->addon->description;
            },
            'price' => function(){
                return (float)$this->price;
            },
            'quantity' => function(){
                return (int)$this->quantity;
            },
            'status' => function () {
                return (bool)$this->addon->status;
            }
        ];
    }
}
