<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "menu_item_addon".
 *
 * @property string $menu_item_id
 * @property string $addon_id
 *
 * @property Addons $addon
 * @property MenuItems $menuItem
 */
class MenuItemAddon extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu_item_addon';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['menu_item_id', 'addon_id'], 'required'],
            [['menu_item_id', 'addon_id'], 'integer'],
            [['addon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Addons::className(), 'targetAttribute' => ['addon_id' => 'id']],
            [['menu_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => MenuItems::className(), 'targetAttribute' => ['menu_item_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'menu_item_id' => 'Menu Item ID',
            'addon_id' => 'Addon ID',
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
    public function getMenuItem()
    {
        return $this->hasOne(MenuItems::className(), ['id' => 'menu_item_id']);
    }

    /**
     * @inheritdoc
     * @return MenuItemAddonQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MenuItemAddonQuery(get_called_class());
    }
    
    public function afterValidate()
    {
        if ($this->hasErrors()) {
            return Helpers::HttpException(422,'validation failed', ['error' => $this->errors]);
        }
    }
    
}
