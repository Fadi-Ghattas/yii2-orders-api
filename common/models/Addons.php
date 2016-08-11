<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "addons".
 *
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string $price
 * @property integer $status
 * @property string $image
 * @property string $restaurant_id
 *
 * @property Restaurants $restaurant
 * @property MenuItemAddon[] $menuItemAddons
 * @property MenuItems[] $menuItems
 */
class Addons extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'addons';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description', 'price', 'status', 'restaurant_id'], 'required'],
            [['price'], 'number'],
            [['status', 'restaurant_id'], 'integer'],
            [['name', 'description', 'image'], 'string', 'max' => 255],
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
            'description' => 'Description',
            'price' => 'Price',
            'status' => 'Status',
            'image' => 'Image',
            'restaurant_id' => 'Restaurant ID',
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
    public function getMenuItemAddons()
    {
        return $this->hasMany(MenuItemAddon::className(), ['addon_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuItems()
    {
        return $this->hasMany(MenuItems::className(), ['id' => 'menu_item_id'])->viaTable('menu_item_addon', ['addon_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return AddonsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AddonsQuery(get_called_class());
    }
}
