<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "menu_item_choice".
 *
 * @property string $menu_item_id
 * @property string $choice_id
 *
 * @property ItemChoices $choice
 * @property MenuItems $menuItem
 */
class MenuItemChoice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu_item_choice';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['menu_item_id', 'choice_id'], 'required'],
            [['menu_item_id', 'choice_id'], 'integer'],
            [['choice_id'], 'exist', 'skipOnError' => true, 'targetClass' => ItemChoices::className(), 'targetAttribute' => ['choice_id' => 'id']],
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
    public function getMenuItem()
    {
        return $this->hasOne(MenuItems::className(), ['id' => 'menu_item_id']);
    }

    /**
     * @inheritdoc
     * @return MenuItemChoiceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MenuItemChoiceQuery(get_called_class());
    }

    public function afterValidate()
    {
        if ($this->hasErrors()) {
            return Helpers::HttpException(422,'validation failed', ['error' => $this->errors]);
        }
    }
}
