<?php

namespace common\models;


use Yii;
use common\helpers\Helpers;

/**
 * This is the model class for table "menu_items".
 *
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string $price
 * @property integer $status
 * @property integer $discount
 * @property string $image
 * @property integer $is_taxable
 * @property integer $is_verified
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property MenuCategoryItem[] $menuCategoryItems
 * @property MenuItemAddon[] $menuItemAddons
 * @property Addons[] $addons
 * @property MenuItemChoice[] $menuItemChoices
 * @property ItemChoices[] $choices
 * @property OrderItems[] $orderItems
 */
class MenuItems extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description', 'price', 'status', 'is_taxable'], 'required'],
            [['price'], 'number'],
            [['status', 'discount', 'is_taxable','is_verified'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['name', 'description', 'image'], 'string', 'max' => 255],
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
            'discount' => 'Discount',
            'image' => 'Image',
            'is_taxable' => 'Is Taxable',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuCategoryItems()
    {
        return $this->hasMany(MenuCategoryItem::className(), ['menu_item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuItemAddons()
    {
        return $this->hasMany(MenuItemAddon::className(), ['menu_item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddons()
    {
        return $this->hasMany(Addons::className(), ['id' => 'addon_id'])->viaTable('menu_item_addon', ['menu_item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuItemChoices()
    {
        return $this->hasMany(MenuItemChoice::className(), ['menu_item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChoices()
    {
        return $this->hasMany(ItemChoices::className(), ['id' => 'choice_id'])->viaTable('menu_item_choice', ['menu_item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItems::className(), ['item_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return MenuItemsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MenuItemsQuery(get_called_class());
    }

    public static function getMenuItem($restaurant_id, $menu_item_id)
    {
        return self::find()->Where(['menu_items.id' => $menu_item_id])
                           ->andWhere(['menu_categories.restaurant_id' => $restaurant_id])
                           ->andWhere(['menu_items.deleted_at' => null])
                           ->andWhere(['menu_categories.deleted_at' => null])
                           ->joinWith(['menuCategoryItems'], true, 'INNER JOIN')
                           ->joinWith(['menuCategoryItems', 'menuCategoryItems.menuCategory'], true, 'INNER JOIN')
                           ->one();
    }

    public static function getMenuItemByName($restaurant_id, $menu_item_name)
    {
        return self::find()->joinWith(['menuCategoryItems'], true, 'INNER JOIN')
            ->joinWith(['menuCategoryItems', 'menuCategoryItems.menuCategory'], true, 'INNER JOIN')
            ->Where(['menu_items.name' => $menu_item_name])
            ->andWhere(['menu_categories.restaurant_id' => $restaurant_id])
            ->andWhere(['menu_items.deleted_at' => null])
            ->andWhere(['menu_categories.deleted_at' => null])
            ->one();
    }

    public static function getRestaurantMenuItem($menu_item_id)
    {
        $restaurant = Restaurants::checkRestaurantAccess();
        $MenuItem = self::getMenuItem($restaurant->id, $menu_item_id);
        if (empty($MenuItem))
            return Helpers::formatResponse(false, 'get failed', ['error' => "this menu item dos't exist"]);

        return Helpers::formatResponse(true, 'get success', $MenuItem);
    }

    public static function createMenuItem($data)
    {
        $restaurant = Restaurants::checkRestaurantAccess();

        $menuItem = new MenuItems();
        foreach ($menuItem->attributes as $attributeKey => $attribute){
            if (isset($data[$attributeKey]))
                $menuItem->$attributeKey = $data[$attributeKey];
        }
        $menuItem->status = 1;
        $menuItem->validate();

        if(!empty(self::getMenuItemByName($restaurant->id, $data['name'])))
            return Helpers::HttpException(422,'validation failed', ['error' => 'There is already menu item with the same name']);

        if(!isset($data['category_id']))
            return Helpers::HttpException(422,'validation failed', ['error' => 'category_id is required']);

        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $menuItem->save();
            $menuCategoryItem = new MenuCategoryItem();
            $menuCategoryItem->menu_item_id = $menuItem->id;
            $menuCategoryItem->menu_category_id = $data['category_id'];
            $menuCategoryItem->save();
            $menuCategoryItem->validate();
            $transaction->commit();
            return Helpers::formatResponse(true, 'create success', ['id' => $menuItem->id]);
        } catch (\Exception $e){
            $transaction->rollBack();
            return Helpers::formatResponse(false, 'create failed', null);
//            throw $e;
        }
        return Helpers::formatResponse(false, 'create failed', null);
    }

    public static function updateMenuItem($menu_item_id, $data)
    {
        $restaurant = Restaurants::checkRestaurantAccess();
        $menuItem = self::getMenuItem($restaurant->id, $menu_item_id);

        if (empty($menuItem))
            return Helpers::HttpException(422,'validation failed', ['error' => "This menu item dos't exist"]);
        $CheckUniqueMenuItem = self::getMenuItemByName($restaurant->id, $data['name']);

        if(!empty($CheckUniqueMenuItem) && $CheckUniqueMenuItem->id != $menu_item_id)
            return Helpers::HttpException(422,'validation failed', ['error' => 'There is already menu item with the same name']);

        if((isset($data['old_category_id']) && !isset($data['category_id'])) || (!isset($data['old_category_id']) && isset($data['category_id'])))
            return Helpers::HttpException(422,'validation failed', ['error' => 'old_category_id and category_id are required']);

        foreach ($data as $DataKey => $DataValue) {
            if (array_key_exists($DataKey, $menuItem->oldAttributes)) {
                $menuItem->$DataKey = $DataValue;
            }
        }

        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $menuItem->save();
            if(isset($data['old_category_id']) && isset($data['category_id']))
            {
                $checkOldCategory = empty(MenuCategories::getCategory($restaurant->id, $data['old_category_id']));
                $checkNewCategory = empty(MenuCategories::getCategory($restaurant->id, $data['category_id']));
                if($checkOldCategory || $checkNewCategory)//check if the old category and the new category belong to the restaurant
                    return Helpers::HttpException(403, "You don't have permission to do this action", null);

                $menuCategoryItem = MenuCategoryItem::findOne(['menu_item_id' => $menuItem->id , 'menu_category_id' => $data['old_category_id']]);
                if(empty($menuCategoryItem)){
                    $menuCategoryItem = new MenuCategoryItem();
                    $menuCategoryItem->menu_item_id = $menuItem->id;
                }
                $menuCategoryItem->menu_category_id = $data['category_id'];
                $menuCategoryItem->validate();
                $menuCategoryItem->save();
            }
            $transaction->commit();
            return Helpers::formatResponse(true, 'update success', ['id' => $menuItem->id]);
        } catch (\Exception $e){
            $transaction->rollBack();
            return Helpers::formatResponse(false, 'update failed', null);
//            throw $e;
        }
        return Helpers::formatResponse(false, 'update failed', null);
    }

    public static function deleteMenuItem($menu_item_id) {

        $restaurant = Restaurants::checkRestaurantAccess();
        $MenuItem = self::getMenuItem($restaurant->id, $menu_item_id);

        if (empty($MenuItem))
            return Helpers::HttpException(422,'validation failed', ['error' => "This menu item dos't exist"]);

        $MenuItem->deleted_at = date('Y-m-d H:i:s');
        $isUpdated = $MenuItem->save();

        if (!$isUpdated)
            return Helpers::formatResponse($isUpdated, 'deleted failed', null);

        return Helpers::formatResponse($isUpdated, 'deleted success', ['id' => $MenuItem->id]);
    }

    public function afterValidate()
    {
        if ($this->hasErrors()) {
            Helpers::HttpException(422,'validation failed', ['error' => $this->errors]);
        }
    }

    public function beforeSave($insert)
    {
        if (!$this->isNewRecord)
            $this->updated_at = date('Y-m-d H:i:s');
        else
            $this->created_at = date('Y-m-d H:i:s');

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function fields()
    {
        return ['id','name','description','price','status','discount','image','is_taxable'];
    }
}
