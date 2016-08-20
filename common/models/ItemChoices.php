<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "item_choices".
 *
 * @property string $id
 * @property string $name
 * @property integer $status
 * @property string $restaurant_id
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Restaurants $restaurant
 * @property MenuItemChoice[] $menuItemChoices
 * @property MenuItems[] $menuItems
 * @property OrderItems[] $orderItems
 */
class ItemChoices extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'item_choices';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'status', 'restaurant_id'], 'required'],
            [['status', 'restaurant_id'], 'integer'],
            [['deleted_at', 'created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
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
            'name' => 'Name',
            'status' => 'Status',
            'restaurant_id' => 'Restaurant ID',
            'deleted_at' => 'Deleted At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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
    public function getMenuItemChoices()
    {
        return $this->hasMany(MenuItemChoice::className(), ['choice_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuItems()
    {
        return $this->hasMany(MenuItems::className(), ['id' => 'menu_item_id'])->viaTable('menu_item_choice', ['choice_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItems::className(), ['choice_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return ItemChoicesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ItemChoicesQuery(get_called_class());
    }
}
