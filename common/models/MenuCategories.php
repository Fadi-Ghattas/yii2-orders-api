<?php

namespace common\models;

use common\helpers\Helpers;
use Yii;

/**
 * This is the model class for table "menu_categories".
 *
 * @property string $id
 * @property string $name
 * @property string $restaurant_id
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

    /**
     * @inheritdoc
     * @return MenuCategoriesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MenuCategoriesQuery(get_called_class());
    }

    public static function getMenuCategories()
    {
        $headers = Yii::$app->getRequest()->getHeaders();
        $restaurantManager = User::findIdentityByAccessToken(explode(' ', $headers['authorization'])[1]);
        $restaurant = Restaurants::find()->where(['user_id' => $restaurantManager->id])->one();

        if(empty($restaurantManager))
            throw new NotFoundHttpException('User not found');
        if(User::getRoleName($restaurantManager->id) != User::RESTAURANT_MANAGER)
            throw new ForbiddenHttpException('This account is not a restaurant account');
        if(!$restaurant->status)
            throw new ForbiddenHttpException('This account is deactivated');

        return ['success' => 'true' , 'message' => 'get success', 'data' => Helpers::formatJsonIdName($restaurant->menuCategories)];
    }

    public static function getMenuCategoryItemsResponse($id)
    {
        $headers = Yii::$app->getRequest()->getHeaders();
        $restaurantManager = User::findIdentityByAccessToken(explode(' ', $headers['authorization'])[1]);
        $restaurant = Restaurants::find()->where(['user_id' => $restaurantManager->id])->one();

        if(empty($restaurantManager))
            throw new NotFoundHttpException('User not found');
        if(User::getRoleName($restaurantManager->id) != User::RESTAURANT_MANAGER)
            throw new ForbiddenHttpException('This account is not a restaurant account');
        if(!$restaurant->status)
            throw new ForbiddenHttpException('This account is deactivated');

        $menuCategoryItems = MenuCategories::find()
            ->where(['menu_categories.id' => $id])
            ->joinWith(['menuCategoryItems'], true, 'INNER JOIN')
            ->joinWith(['menuCategoryItems', 'menuCategoryItems.menuItem'], true, 'INNER JOIN')
            ->asArray()->all();

        if(empty($menuCategoryItems))
            return ['success' => 'true' , 'message' => 'get success', 'data' => $menuCategoryItems];
        if(!is_null($menuCategoryItems[0]['deleted_at']))
            return Helpers::UnprocessableEntityHttpException("This menu category was deleted and we can't get the menu items", null);
        if($restaurant->id != intval($menuCategoryItems[0]['restaurant_id']))
            return Helpers::UnprocessableEntityHttpException('This menu category is not belong to this restaurant',null);

        $menuItems = array();
        foreach ($menuCategoryItems[0]['menuCategoryItems'] as $menuItem)
        {
            if(!empty($menuItem['menuItem'])) {
                $singleMenuItem = array();
                $singleMenuItem[$menuItem['menuItem']['id']] = $menuItem['menuItem']['name'];
                $menuItems[] = $singleMenuItem;
            }
        }

        return ['success' => 'true' , 'message' => 'get success', 'data' => $menuItems];
    }
}
