<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "menu_category_item".
 *
 * @property string $id
 * @property string $menu_category_id
 * @property string $menu_item_id
 *
 * @property MenuCategories $menuCategory
 * @property MenuItems $menuItem
 */
class MenuCategoryItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu_category_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['menu_category_id', 'menu_item_id'], 'required'],
            [['menu_category_id', 'menu_item_id'], 'integer'],
            [['menu_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => MenuCategories::className(), 'targetAttribute' => ['menu_category_id' => 'id']],
            [['menu_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => MenuItems::className(), 'targetAttribute' => ['menu_item_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'menu_category_id' => 'Menu Category ID',
            'menu_item_id' => 'Menu Item ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuCategory()
    {
        return $this->hasOne(MenuCategories::className(), ['id' => 'menu_category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuItem()
    {
        return $this->hasOne(MenuItems::className(), ['id' => 'menu_item_id'])->where(['menu_items.deleted_at' => null]);
    }

    /**
     * @inheritdoc
     * @return MenuCategoryItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MenuCategoryItemQuery(get_called_class());
    }
    
}
