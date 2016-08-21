<?php

namespace common\models;

use common\helpers\Helpers;
use Yii;
use yii\web\ForbiddenHttpException;

/**
 * This is the model class for table "menu_categories".
 *
 * @property string $id
 * @property string $name
 * @property string $restaurant_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property Restaurants $restaurant
 * @property MenuCategoryItem[] $menuCategoryItems
 */
class MenuCategories extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu_categories';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'restaurant_id'], 'required'],
            [['restaurant_id'], 'integer'],
            [['deleted_at'], 'safe'],
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
            'restaurant_id' => 'Restaurant ID',
            'deleted_at' => 'Deleted At',
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
    public function getMenuCategoryItems()
    {
        return $this->hasMany(MenuCategoryItem::className(), ['menu_category_id' => 'id']);
    }

    public static function getMenuCategoryItemsAsArray($category_id)
    {
        return MenuCategories::find()
                                ->where(['menu_categories.id' => $category_id])
                                ->joinWith(['menuCategoryItems'], true, 'INNER JOIN')
                                ->joinWith(['menuCategoryItems', 'menuCategoryItems.menuItem'], true, 'INNER JOIN')
                                ->orderBy('menu_items.created_at DESC')
                                ->asArray()->all();
    }
    /**
     * @inheritdoc
     * @return MenuCategoriesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MenuCategoriesQuery(get_called_class());
    }

    public static function getCategory($category_id)
    {
        return MenuCategories::find()->where(['id' => $category_id])->andWhere(['deleted_at' => null])->one();
    }

    public static function isCategoryDeleted($category_id)
    {
        return empty(self::getCategory($category_id));
    }

    public static function getMenuCategories()
    {
        $restaurant = Restaurants::checkRestaurantAccess();
        if(empty($restaurant->menuCategories))
            return Helpers::formatResponse(false, 'get failed', ['error' => 'restaurant has no categories']);

        return Helpers::formatResponse(true, 'get success', Helpers::formatJsonIdName($restaurant->menuCategories));
    }

    public static function getMenuCategoryItemsResponse($category_id)
    {
        $restaurant = Restaurants::checkRestaurantAccess();

        if(self::isCategoryDeleted($category_id))
            return Helpers::formatResponse(false, 'get failed', ['error' => "This category dos't exist"]);

        $menuCategoryItems = self::getMenuCategoryItemsAsArray($category_id);
        if(empty($menuCategoryItems))
            return Helpers::formatResponse(false, 'get failed', ['error' => 'this category is empty']);
        if(!is_null($menuCategoryItems[0]['deleted_at']))
            return Helpers::UnprocessableEntityHttpException('validation failed', ['error' => "This menu category was deleted and we can't get the menu items"]);
        if($restaurant->id != intval($menuCategoryItems[0]['restaurant_id']))
            return Helpers::UnprocessableEntityHttpException('validation failed', ['error' => 'This menu category is not belong to this restaurant']);

        $menuItems = array();
        foreach ($menuCategoryItems[0]['menuCategoryItems'] as $menuItem)
        {
            if(!empty($menuItem['menuItem'])) {
                $singleMenuItem = array();
                $singleMenuItem['id'] = $menuItem['menuItem']['id'];
                $singleMenuItem['name'] = $menuItem['menuItem']['name'];
                $singleMenuItem['price'] = $menuItem['menuItem']['price'];
                $singleMenuItem['status'] = $menuItem['menuItem']['status'];
                $menuItems[] = $singleMenuItem;
            }
        }

        return Helpers::formatResponse(true, 'get success', $menuItems);
    }

    public static function createCategory($data)
    {
        $restaurant = Restaurants::checkRestaurantAccess();

        if(!isset($data['name']))
            return Helpers::UnprocessableEntityHttpException('validation failed', ['error' => 'name is required']);

        if(self::IsRestaurantMenuCategoryNameUnique($restaurant->getMenuCategoriesAsArray(), $data['name'])){
            $menCategory = new MenuCategories();
            $menCategory->name = $data['name'];
            $menCategory->restaurant_id = $restaurant->id;
            $isCreated = $menCategory->save();
            if(!$isCreated)
                return Helpers::formatResponse($isCreated, 'create failed', null);

            return Helpers::formatResponse($isCreated, 'create success', ['id' => $menCategory->id]);
        }
        return Helpers::UnprocessableEntityHttpException('validation failed', ['error' => 'There is already category with the same name']);
    }

    public static function updateCategory($category_id, $data)
    {
        $restaurant = Restaurants::checkRestaurantAccess();

        if(!isset($data['name']))
            return Helpers::UnprocessableEntityHttpException('validation failed', ['error' => 'name is required']);

        if(self::IsRestaurantMenuCategoryNameUnique($restaurant->getMenuCategoriesAsArray(), $data['name']))
        {
            $menCategory = self::getCategory($category_id);

            if(is_null($menCategory))
                return Helpers::UnprocessableEntityHttpException('validation failed', ['error' => "This category dos't exist"]);

            if($menCategory->restaurant_id != $restaurant->id)
                throw new ForbiddenHttpException("You don't have permission to do this action");

            $menCategory->name = $data['name'];
            $isUpdated = $menCategory->save();
            if(!$isUpdated)
                return Helpers::formatResponse($isUpdated, 'update failed', null);
            
            return Helpers::formatResponse($isUpdated, 'update success', ['id' => $menCategory->id]);
        }
        return Helpers::UnprocessableEntityHttpException('validation failed', ['error' => 'There is already menu category with the same name']);
    }

    public static function deleteCategory($category_id)
    {
        $restaurant = Restaurants::checkRestaurantAccess();
        if(empty(self::getMenuCategoryItemsAsArray($category_id))) {

            $menCategory = self::getCategory($category_id);;

            if(is_null($menCategory))
                return Helpers::UnprocessableEntityHttpException('validation failed', ['error' => "This category dos't exist"]);

            if($menCategory->restaurant_id != $restaurant->id)
                throw new ForbiddenHttpException("You don't have permission to do this action");

            $menCategory->deleted_at = date('Y-m-d H:i:s');
            $isUpdated = $menCategory->save();

            if(!$isUpdated)
                return Helpers::formatResponse($isUpdated, 'deleted failed', null);

            return Helpers::formatResponse($isUpdated, 'deleted success', ['id' => $menCategory->id]);
        }
        return Helpers::UnprocessableEntityHttpException('validation failed', ['error' => 'There is already menu category items with this category']);
    }

    public static function IsRestaurantMenuCategoryNameUnique($restaurantMenuCategories, $NewMenuCategoryName)
    {
        foreach ($restaurantMenuCategories as $MenuCategories){
            if($MenuCategories['name'] == $NewMenuCategoryName)
                return false;
        }
        return true;
    }

    public function afterValidate() {
        if ($this->hasErrors()) {
            Helpers::UnprocessableEntityHttpException('validation failed' ,  ['error' => $this->errors]);
        }
    }

    public function beforeSave($insert)
    {
        if(!$this->isNewRecord)
            $this->updated_at = date('Y-m-d H:i:s');
        else
            $this->created_at = date('Y-m-d H:i:s');

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }
}