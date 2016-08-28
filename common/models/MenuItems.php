<?php

namespace common\models;


use Yii;
use common\helpers\Helpers;
use yii\helpers\Json;

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
            [['created_at', 'updated_at', 'deleted_at','name'], 'safe'],
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
//        return MenuCategoryItem::find()->where(['menu_item_id' => $this->id])->all();
    }

    public function getRelatedMenuCategoryItems(){
        return MenuCategoryItem::find()->where(['menu_item_id' => $this->id])->all();
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
        return $this->hasMany(Addons::className(), ['id' => 'addon_id'])->viaTable('menu_item_addon', ['menu_item_id' => 'id'])
            ->where(['deleted_at' => null]);
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
        return $this->hasMany(ItemChoices::className(), ['id' => 'choice_id'])->viaTable('menu_item_choice', ['menu_item_id' => 'id'])
            ->where(['deleted_at' => null]);
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

    public static function getMenuItemCategories($restaurant_id, $menu_item_id){
        return MenuCategories::find()
            ->where(['menu_categories.restaurant_id' => $restaurant_id])
            ->andWhere(['menu_items.id' => $menu_item_id])
            ->andWhere(['menu_items.deleted_at' => null])
            ->andWhere(['menu_categories.deleted_at' => null])
            ->joinWith(['menuCategoryItems'], true, 'INNER JOIN')
            ->joinWith(['menuCategoryItems', 'menuCategoryItems.menuItem'], true, 'INNER JOIN')
            ->select(['menu_categories.id','menu_categories.name'])
            ->orderBy('menu_items.created_at DESC')->all();
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

        if(!isset($data['categories']))
            return Helpers::HttpException(422,'validation failed', ['error' => 'one category at least is required']);

        if(empty($data['categories']))
            return Helpers::HttpException(422,'validation failed', ['error' => "categories can't be blank"]);

        if(isset($data['categories']) && !empty($data['categories']))
        {
            foreach ($data['categories'] as $category) {
                if(empty(MenuCategories::getCategory($restaurant->id, $category['id'])))//check's if addOn belong to the restaurant
                    return Helpers::HttpException(422,'validation failed', ['error' => "There is category dos't exist"]);
            }
        }
        if(isset($data['addOns']) && !empty($data['addOns']))
        {
            foreach ($data['addOns'] as $addOn) {
                if(empty(Addons::getAddOn($restaurant->id, $addOn['id'])))//check's if addOn belong to the restaurant
                    return Helpers::HttpException(422,'validation failed', ['error' => "There is add-on dos't exist"]);
            }
        }

        if(isset($data['ItemChoices']) && !empty($data['ItemChoices']))
        {
            foreach ($data['ItemChoices'] as $itemChoices){
                if(empty(ItemChoices::getItemChoice($restaurant->id, $itemChoices['id'])))//check's if item choices belong to the restaurant
                    return Helpers::HttpException(422,'validation failed', ['error' => "There is item choices dos't exist"]);
            }
        }

        $menuItem = new MenuItems();
        $model['MenuItems'] = $data;
        $menuItem->load($model);
        $menuItem->status = 1;
        $menuItem->validate();

        if(!empty(self::getMenuItemByName($restaurant->id, $data['name'])))
            return Helpers::HttpException(422,'validation failed', ['error' => 'There is already menu item with the same name']);

        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $menuItem->save();

            foreach ($data['categories'] as $category) {
                $menuCategoryItem = new MenuCategoryItem();
                $menuCategoryItem->menu_item_id = $menuItem->id;
                $menuCategoryItem->menu_category_id = $category['id'];
                $menuCategoryItem->save();
                $menuCategoryItem->validate();
            }

            if(isset($data['addOns']) && !empty($data['addOns']))
            {
                foreach ($data['addOns'] as $addOn){
                    $menuItemAddon = new MenuItemAddon();
                    $menuItemAddon->addon_id = $addOn['id'];
                    $menuItemAddon->menu_item_id = $menuItem->id;
                    $menuItemAddon->validate();
                    $menuItemAddon->save();
                }
            }
            if(isset($data['ItemChoices']) && !empty($data['ItemChoices']))
            {
                foreach ($data['ItemChoices'] as $itemChoices){
                    $menuItemChoice = new MenuItemChoice();
                    $menuItemChoice->choice_id = $itemChoices['id'];
                    $menuItemChoice->menu_item_id = $menuItem->id;
                    $menuItemChoice->validate();
                    $menuItemChoice->save();
                }
            }
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

        
        $model['MenuItems'] = $data;
        $menuItem->load($model);
        $menuItem->validate();

        if(isset($data['name'])) {
            $CheckUniqueMenuItem = self::getMenuItemByName($restaurant->id, $data['name']);
            if(!empty($CheckUniqueMenuItem) && $CheckUniqueMenuItem->id != $menu_item_id)
                return Helpers::HttpException(422,'validation failed', ['error' => 'There is already menu item with the same name']);
        }

        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {

            $menuItem->save();

            if(isset($data['categories'])) {

                if(empty($data['categories']))
                    return Helpers::HttpException(422,'validation failed', ['error' => "categories can't be blank"]);

                $newCategories = $data['categories'];
                $menuCategoryItems = $menuItem->getRelatedMenuCategoryItems();

                $models = [];
                foreach ($menuCategoryItems as $MenuCategoryItem) {
                    $models[$MenuCategoryItem->id] = $MenuCategoryItem;
                }

                $menuCategoryItems = $models;

                foreach ($newCategories as $Category) {

                    if(!isset($Category['id']))
                        return Helpers::HttpException(422,'validation failed', ['error' => "category id is required"]);
                    if(empty($Category['id']))
                        return Helpers::HttpException(422,'validation failed', ['error' => "category id can't be blank"]);

                    if (!array_key_exists($Category['id'], $menuCategoryItems)) {

                        if(empty(MenuCategories::getCategory($restaurant->id, $Category['id'])))
                            return Helpers::HttpException(422,'validation failed', ['error' => "There is a category dos't exist"]);

                        $menuCategoryItem = new MenuCategoryItem();
                        $menuCategoryItem->menu_category_id = $Category['id'];
                        $menuCategoryItem->menu_item_id = $menuItem->id;
                        $menuCategoryItem->validate();
                        $menuCategoryItem->save();
                    } else {
                        unset($menuCategoryItems[$Category['id']]);
                    }
                }

                if (!empty($menuCategoryItems))
                    foreach ($menuCategoryItems as $MenuCategoryItem)
                        $MenuCategoryItem->delete();
            }

            if(isset($data['addOns'])) {

                if(empty($data['addOns']))
                    return Helpers::HttpException(422,'validation failed', ['error' => "addOns can't be blank"]);

                $newAddons = $data['addOns'];
                $menuItemAddons = $menuItem->menuItemAddons;

                $models = [];
                foreach ($menuItemAddons as $MenuItemAddon) {
                    $models[$MenuItemAddon->addon_id] = $MenuItemAddon;
                }

                $menuItemAddons = $models;

                foreach ($newAddons as $Addon) {

                    if(!isset($Addon['id']))
                        return Helpers::HttpException(422,'validation failed', ['error' => "add-on id is required"]);
                    if(empty($Addon['id']))
                        return Helpers::HttpException(422,'validation failed', ['error' => "add-on id can't be blank"]);

                    if (!array_key_exists($Addon['id'], $menuItemAddons)) {

                        if(empty(Addons::getAddOn($restaurant->id, $Addon['id'])))
                            return Helpers::HttpException(422,'validation failed', ['error' => "There add-on dos't exist"]);

                        $menuItemAddon = new MenuItemAddon();
                        $menuItemAddon->addon_id = $Addon['id'];
                        $menuItemAddon->menu_item_id = $menuItem->id;
                        $menuItemAddon->validate();
                        $menuItemAddon->save();
                    } else {
                        unset($menuItemAddons[$Addon['id']]);
                    }
                }

                if (!empty($menuItemAddons))
                    foreach ($menuItemAddons as $MenuItemAddon)
                        $MenuItemAddon->delete();
            }


            if(isset($data['ItemChoices'])) {

                if(empty($data['ItemChoices']))
                    return Helpers::HttpException(422,'validation failed', ['error' => "ItemChoices can't be blank"]);

                $newItemChoices = $data['ItemChoices'];
                $menuItemChoices = $menuItem->menuItemChoices;

                $models = [];
                foreach ($menuItemChoices as $MenuItemChoice) {
                    $models[$MenuItemChoice->choice_id] = $MenuItemChoice;
                }

                $menuItemChoices = $models;

                foreach ($newItemChoices as $ItemChoice) {

                    if(!isset($ItemChoice['id']))
                        return Helpers::HttpException(422,'validation failed', ['error' => "item choice id is required"]);
                    if(empty($ItemChoice['id']))
                        return Helpers::HttpException(422,'validation failed', ['error' => "item choice id can't be blank"]);

                    if (!array_key_exists($ItemChoice['id'], $menuItemChoices)) {

                        if(empty(MenuItems::getMenuItem($restaurant->id, $ItemChoice['id'])))
                            return Helpers::HttpException(422,'validation failed', ['error' => "There item choice dos't exist"]);

                        $menuItemChoice = new MenuItemChoice();
                        $menuItemChoice->choice_id = $ItemChoice['id'];
                        $menuItemChoice->menu_item_id = $menuItem->id;
                        $menuItemChoice->validate();
                        $menuItemChoice->save();
                    } else {
                        unset($menuItemChoices[$ItemChoice['id']]);
                    }
                }

                if (!empty($menuItemChoices))
                    foreach ($menuItemChoices as $MenuItemChoice)
                        $MenuItemChoice->delete();
            }

            $transaction->commit();
            return Helpers::formatResponse(true, 'update success', ['id' => $menuItem->id]);
        } catch (\Exception $e) {
            $transaction->rollBack();
            return Helpers::formatResponse(false, 'update failed', null);
            //throw $e;
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
            return Helpers::HttpException(422,'validation failed', ['error' => $this->errors]);
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
        return ['id',
                'name',
                'description',
                'price',
                'status',
                'discount',
                'image',
                'is_taxable',
                'is_verified',
                'categories' => function() {
                  $restaurant = Restaurants::checkRestaurantAccess();
                  return self::getMenuItemCategories($restaurant->id, $this->id);
                },
                'addOns' => function(){
                    return $this->addons;
                },
                'ItemChoices'=> function(){
                    return $this->choices;
                }
        ];
    }
}
