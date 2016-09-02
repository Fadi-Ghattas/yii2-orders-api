<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "order_item_choices".
 *
 * @property string $order_item_id
 * @property string $item_choice_id
 * @property string $price
 *
 * @property ItemChoices $itemChoice
 * @property OrderItems $orderItem
 */
class OrderItemChoices extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_item_choices';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_item_id', 'item_choice_id'], 'required'],
            [['order_item_id', 'item_choice_id'], 'integer'],
            [['price'], 'number'],
            [['item_choice_id'], 'exist', 'skipOnError' => true, 'targetClass' => ItemChoices::className(), 'targetAttribute' => ['item_choice_id' => 'id']],
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
            'item_choice_id' => 'Item Choice ID',
            'price' => 'Price',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemChoice()
    {
        return $this->hasOne(ItemChoices::className(), ['id' => 'item_choice_id']);
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
     * @return OrderItemChoicesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrderItemChoicesQuery(get_called_class());
    }
}
