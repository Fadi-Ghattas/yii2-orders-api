<?php

namespace common\models;


use Yii;
use yii\helpers\ArrayHelper;
use common\helpers\Helpers;

/**
 * This is the model class for table "menu_categories".
 *
 * @property string             $id
 * @property string             $name
 * @property string             $restaurant_id
 * @property string             $created_at
 * @property string             $updated_at
 * @property string             $deleted_at
 *
 * @property Restaurants        $restaurant
 * @property MenuCategoryItem[] $menuCategoryItems
 */
class MenuCategories extends \yii\db\ActiveRecord
{
    const SCENARIO_GET_BY_RESTAURANTS_MANGER = 'get_by_restaurants_manger';
    const SCENARIO_GET_DETAILS_BY_CLIENT = 'get_details_by_client';

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
            [['restaurant_id'], 'exist', 'skipOnError' => TRUE, 'targetClass' => Restaurants::className(), 'targetAttribute' => ['restaurant_id' => 'id']],
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


    /**
     * @inheritdoc
     * @return MenuCategoriesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MenuCategoriesQuery(get_called_class());
    }

    public static function getMenuCategoryItemsAsArray($restaurant_id, $category_id)
    {
        return MenuItems::find()->joinWith(['menuCategoryItems'])
            ->where(['menu_category_item.menu_category_id' => $category_id])
            ->andWhere(['deleted_at' => NULL])
            ->orderBy('updated_at DESC')->orderBy('created_at DESC')->all();
    }

    public static function getCategory($restaurant_id, $category_id)
    {
        return MenuCategories::find()->where(['restaurant_id' => $restaurant_id])->andWhere(['id' => $category_id])->andWhere(['deleted_at' => NULL])->one();
    }

    public static function getMenuCategoryByName($restaurant_id, $category_name)
    {
        return self::find()->where(['restaurant_id' => $restaurant_id])->andWhere(['name' => $category_name])->andWhere(['deleted_at' => NULL])->one();
    }

    public static function isCategoryDeleted($restaurant_id, $category_id)
    {
        return empty(self::getCategory($restaurant_id, $category_id));
    }

    public static function getMenuCategories()
    {
        $restaurant = Restaurants::checkRestaurantAccess();
        return Helpers::formatResponse(TRUE, 'get success', $restaurant->menuCategories);
    }

    public static function getMenuCategoryItemsResponse($category_id)
    {
        $restaurant = Restaurants::checkRestaurantAccess();

        $menuCategory = MenuCategories::find()->where(['restaurant_id' => $restaurant->id])->andWhere(['id' => $category_id])->one();
        if (empty($menuCategory))
            return Helpers::HttpException(404, 'get failed', ['error' => "This category dos't exist"]);
        if (!empty($menuCategory->deleted_at))
            return Helpers::HttpException(422, 'validation failed', ['error' => "This menu category was deleted and we can't get the menu items"]);

        $menuCategoryItems = self::getMenuCategoryItemsAsArray($restaurant->id, $category_id);
        $menuItems = [];
        foreach ($menuCategoryItems as $menuItem) {
            $singleMenuItem = [];
            $singleMenuItem['id'] = (int)$menuItem->id;
            $singleMenuItem['name'] = (string)$menuItem->name;
            $singleMenuItem['price'] = (double)$menuItem->price;
            $singleMenuItem['status'] = (bool)$menuItem->status;
            $singleMenuItem['is_verified'] = (bool)$menuItem->is_verified;
            $menuItems[] = $singleMenuItem;
        }

        return Helpers::formatResponse(TRUE, 'get success', $menuItems);
    }

    public static function createCategory($data)
    {
        $restaurant = Restaurants::checkRestaurantAccess();

        $menCategory = new MenuCategories();
        $model['MenuCategories'] = $data;
        $menCategory->load($model);
        $menCategory->restaurant_id = $restaurant->id;
        $menCategory->validate();

        if (self::getMenuCategoryByName($restaurant->id, $data['name']))
            return Helpers::HttpException(409, 'name conflict', ['error' => 'There is already category with the same name']);

        $isCreated = $menCategory->save();
        if (!$isCreated)
            return Helpers::HttpException(422, 'create failed', NULL);
        return Helpers::formatResponse(TRUE, 'create success', ['id' => $menCategory->id]);
    }

    public static function updateCategory($category_id, $data)
    {
        $restaurant = Restaurants::checkRestaurantAccess();
        $menCategory = self::getCategory($restaurant->id, $category_id);

        if (empty($menCategory))
            return Helpers::HttpException(404, 'update failed', ['error' => "This category dos't exist"]);

        $model['MenuCategories'] = $data;
        $menCategory->load($model);
        $menCategory->validate();

        if (isset($data['name'])) {
            $CheckUniqueMenuCategory = self::getMenuCategoryByName($restaurant->id, $data['name']);
            if (!empty($CheckUniqueMenuCategory) && $CheckUniqueMenuCategory->id != $category_id)
                return Helpers::HttpException(409, 'name conflict', ['error' => 'There is already menu category with the same name']);
        }

        $isUpdated = $menCategory->save();
        if (!$isUpdated)
            return Helpers::HttpException(422, 'update failed', NULL);

        return Helpers::formatResponse(TRUE, 'update success', ['id' => $menCategory->id]);
    }

    public static function deleteCategory($category_id)
    {
        $restaurant = Restaurants::checkRestaurantAccess();

        if (!empty(self::getMenuCategoryItemsAsArray($restaurant->id, $category_id)))
            return Helpers::HttpException(422, 'validation failed', ['error' => 'There is already menu category items with this category']);

        $menCategory = self::getCategory($restaurant->id, $category_id);

        if (empty($menCategory))
            return Helpers::HttpException(404, 'deleted failed', ['error' => "This category dos't exist"]);

        $menCategory->deleted_at = date('Y-m-d H:i:s');
        $isUpdated = $menCategory->save();

        if (!$isUpdated)
            return Helpers::HttpException(422, 'deleted failed', NULL);

        return Helpers::formatResponse(TRUE, 'deleted success', ['id' => $menCategory->id]);
    }

    public function afterValidate()
    {
        if ($this->hasErrors()) {
            return Helpers::HttpException(422, 'validation failed', ['error' => $this->errors]);
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

    public function scenarios()
    {
        return ArrayHelper::merge(
            parent::scenarios(),
            [
                self::SCENARIO_GET_BY_RESTAURANTS_MANGER => [
                    'id' => function () {
                        return (int)$this->id;
                    },
                    'name' => function () {
                        return (string)$this->name;
                    },
                ],
                self::SCENARIO_GET_DETAILS_BY_CLIENT => [
                    'id' => function () {
                        return (int)$this->id;
                    },
                    'name' => function () {
                        return (string)$this->name;
                    },
                    'menuCategoriesItems' => function () {
                        $menuCategoryItems = self::getMenuCategoryItemsAsArray($this->restaurant_id, $this->id);
                        if (!empty($menuCategoryItems))
                            return MenuItems::formatMenuCategoryItems($menuCategoryItems, $this->restaurant->is_verified_global);
                        else return [];
                    },
                ],
            ]);
    }

    public function fields()
    {
        $request_action = explode('/', Yii::$app->getRequest()->getUrl());
        if (in_array('clients', $request_action) && Yii::$app->request->isGet && isset(Yii::$app->request->get()['id'])) {
            return $this->scenarios()[self::SCENARIO_GET_DETAILS_BY_CLIENT];
        } else {
            return $this->scenarios()[self::SCENARIO_GET_BY_RESTAURANTS_MANGER];
        }
        return parent::fields();
    }

}