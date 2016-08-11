<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "order_items".
 *
 * @property string $id
 * @property string $order_id
 * @property string $item_id
 * @property string $price
 * @property integer $quantity
 * @property string $addon
 * @property string $note
 * @property string $created_at
 * @property string $updated_at
 * @property string $choice_id
 *
 * @property ItemChoices $choice
 * @property MenuItems $item
 * @property Orders $order
 */
class OrderItems extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'item_id', 'price', 'quantity', 'addon', 'choice_id'], 'required'],
            [['order_id', 'item_id', 'quantity', 'choice_id'], 'integer'],
            [['price'], 'number'],
            [['note'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['addon'], 'string', 'max' => 255],
            [['choice_id'], 'exist', 'skipOnError' => true, 'targetClass' => ItemChoices::className(), 'targetAttribute' => ['choice_id' => 'id']],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => MenuItems::className(), 'targetAttribute' => ['item_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Orders::className(), 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'item_id' => 'Item ID',
            'price' => 'Price',
            'quantity' => 'Quantity',
            'addon' => 'Addon',
            'note' => 'Note',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'choice_id' => 'Choice ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChoice()
    {
        return $this->hasOne(ItemChoices::className(), ['id' => 'choice_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(MenuItems::className(), ['id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Orders::className(), ['id' => 'order_id']);
    }

    /**
     * @inheritdoc
     * @return OrderItemsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrderItemsQuery(get_called_class());
    }
}
