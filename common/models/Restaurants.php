<?php

namespace common\models;


use Yii;
use yii\web\ForbiddenHttpException;
use common\helpers\Helpers;

/**
 * This is the model class for table "restaurants".
 *
 * @property string $id
 * @property string $name
 * @property string $minimum_order_amount
 * @property string $time_order_open
 * @property string $time_order_close
 * @property string $delivery_fee
 * @property string $rank
 * @property integer $halal
 * @property integer $featured
 * @property string $working_opening_hours
 * @property string $working_closing_hours
 * @property integer $disable_ordering
 * @property integer $delivery_duration
 * @property string $phone_number
 * @property double $longitude
 * @property double $latitude
 * @property string $image
 * @property string $image_background
 * @property string $contact_number
 * @property integer $status
 * @property integer $is_verified_global
 * @property string $created_at
 * @property string $updated_at
 * @property string $logout_at
 * @property integer $user_id
 *
 * @property Addons[] $addons
 * @property AreaRestaurant[] $areaRestaurants
 * @property Areas[] $areas
 * @property BlacklistedClients[] $blacklistedClients
 * @property CuisineRestaurant[] $cuisineRestaurants
 * @property Cuisines[] $cuisines
 * @property FavoriteRestaurants[] $favoriteRestaurants
 * @property ItemChoices[] $itemChoices
 * @property MenuCategories[] $menuCategories
 * @property Orders[] $orders
 * @property PaymentMethodRestaurant[] $paymentMethodRestaurants
 * @property User $user
 * @property Owners $owner
 * @property Reviews[] $reviews
 */
class Restaurants extends \yii\db\ActiveRecord
{
    public $action;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'restaurants';
    }

//    public function behaviors()
//    {
//        return [
//            'verbs' => [
//                'class' => \yii\filters\VerbFilter::className(),
//                'actions' => [
//                    'view'   => ['get'],
//                    'create' => ['post'],
//                    'update' => ['put'],
//                    //'delete' => ['post', 'delete'],
//                    'delete' => [''],
//                ],
//            ],
//        ];
//    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'minimum_order_amount', 'time_order_open', 'time_order_close', 'delivery_fee', 'rank', 'halal', 'featured', 'working_opening_hours', 'working_closing_hours', 'disable_ordering', 'delivery_duration', 'phone_number', 'contact_number', 'longitude','latitude', 'image', 'status', 'user_id'], 'required'],
            [['minimum_order_amount', 'delivery_fee', 'rank', 'longitude', 'latitude'], 'number'],
            [['action','time_order_open', 'time_order_close', 'working_opening_hours', 'working_closing_hours', 'created_at', 'updated_at'], 'safe'],
            [['halal', 'featured', 'disable_ordering', 'delivery_duration', 'status', 'user_id', 'is_verified_global'], 'integer'],
            [['name', 'phone_number', 'contact_number' ,'image', 'image_background'], 'string', 'max' => 255],
            [['working_opening_hours','working_closing_hours','time_order_open', 'time_order_close'], 'date', 'format' => 'H:m:s'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
//            [['phone_number'],  'udokmeci\yii2PhoneValidator\PhoneValidator','country'=> 'MY', 'strict'=>false],
//            [['contact_number'], 'number', 'numberPattern' => '/^\s*[-+]?[0-9]*[.,]?[0-9]+([eE][-+]?[0-9]+)?\s*$/'],
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
            'minimum_order_amount' => 'Minimum Order Amount',
            'time_order_open' => 'Time Order Open',
            'time_order_close' => 'Time Order Close',
            'delivery_fee' => 'Delivery Fee',
            'rank' => 'Rank',
            'halal' => 'Halal',
            'featured' => 'Featured',
            'disable_ordering' => 'Disable Ordering',
            'delivery_duration' => 'Delivery Duration',
            'phone_number' => 'Phone Number',
            'working_opening_hours' => 'Working Opening Hours',
            'working_closing_hours' => 'Working Closing Hours',
            'longitude' => 'Longitude',
            'latitude' => 'Latitude',
            'image' => 'Image',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'user_id' => 'User ID',
            'mangerName' => 'Manger Name',
            'mangerEmail' => 'Manger Email',
            'mangerPassWord' => 'Manger PassWord',
            'ownerName' => 'Owner Name',
            'ownerContactNumber' => 'Owner Contact Number',
            'ownerEmail' => 'Owner Email'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddons()
    {
        return $this->hasMany(Addons::className(), ['restaurant_id' => 'id'])->where(['deleted_at' => null]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAreaRestaurants()
    {
        return $this->hasMany(AreaRestaurant::className(), ['restaurant_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAreas()
    {
        return $this->hasMany(Areas::className(), ['id' => 'area_id'])->viaTable('area_restaurant', ['restaurant_id' => 'id'])->where(['deleted_at' => null]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBlacklistedClients()
    {
        return $this->hasMany(BlacklistedClients::className(), ['restaurant_id' => 'id'])->where(['deleted_at' => null]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCuisineRestaurants()
    {
        return $this->hasMany(CuisineRestaurant::className(), ['restaurant_id' => 'id'])->where(['deleted_at' => null]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCuisines()
    {
        return $this->hasMany(Cuisines::className(), ['id' => 'cuisine_id'])->viaTable('cuisine_restaurant', ['restaurant_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFavoriteRestaurants()
    {
        return $this->hasMany(FavoriteRestaurants::className(), ['restaurant_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemChoices()
    {
        return $this->hasMany(ItemChoices::className(), ['restaurant_id' => 'id'])->Where(['deleted_at' => null]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuCategories()
    {
        return $this->hasMany(MenuCategories::className(), ['restaurant_id' => 'id'])->where(['deleted_at' => null]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Orders::className(), ['restaurant_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentMethodRestaurants()
    {
        return $this->hasMany(PaymentMethodRestaurant::className(), ['restaurant_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReviews()
    {
        return $this->hasMany(Reviews::className(), ['restaurant_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return RestaurantsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RestaurantsQuery(get_called_class());
    }

    public function fields()
    {
        return [
            'id',
            'name',
            'email' => function () {
                return $this->user->email;
            },
            'phone_number',
            'contact_number',
            'minimum_order_amount',
            'working_opening_hours',
            'working_closing_hours',
            'time_order_open',
            'time_order_close',
            'delivery_fee',
            'halal',
            'featured',
            'disable_ordering',
            'delivery_duration',
            'longitude',
            'latitude',
            'image',
            'image_background',
            'areas' => function() {
                return $this->areas;
            },
            'cuisine' => function(){
                return  $this->cuisines;
            },
            'paymentMethod' => function(){
                return $this->paymentMethodRestaurants;
            }
        ];
    }

    public function afterFind()
    {
        if(!$this->status)
            throw new ForbiddenHttpException('This account is deactivated');

        parent::afterFind(); // TODO: Change the autogenerated stub
    }

    public function beforeSave($insert)
    {
        $post_data = Yii::$app->request->post();
        if(empty($post_data))
            return Helpers::HttpException(422, 'validation failed', ['error' => ['please provide data']]);

        $headers = Yii::$app->getRequest()->getHeaders();
        $auth_key = explode(' ',$headers['authorization'])[1];
        $restaurantManager = User::findIdentityByAccessToken($auth_key);
        if(empty($restaurantManager))
            throw new NotFoundHttpException('User not found');
        if(User::getRoleName($restaurantManager->id) != User::RESTAURANT_MANAGER)
            throw new ForbiddenHttpException('This account is not a restaurant account');
        if(!$this->oldAttributes['status'])
            throw new ForbiddenHttpException('This account is deactivated');
        if($this->user_id != $restaurantManager->id){
            throw new ForbiddenHttpException("You don't have permission to do this action");
        }

        $lockedValues = ['name', 'halal','rank' ,'featured', 'latitude', 'longitude', 'image', 'status', 'created_at', 'updated_at'];

        if(!$this->action == 'logout')
            $lockedValues [] = 'email';

        foreach ($post_data as $lockedValueKey => $lockedValue) {
            if (in_array($lockedValueKey,$lockedValues))
                throw new ForbiddenHttpException($lockedValueKey . " can't be changed");
        }

        if(!$this->isNewRecord)
            $this->updated_at = date('Y-m-d H:i:s');
        else
            $this->created_at = date('Y-m-d H:i:s');

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function afterValidate(){
        if ($this->hasErrors()) {
            return Helpers::HttpException(422,'validation failed' ,  ['error' => $this->errors]);
        }
    }

    public static function checkRestaurantAccess()
    {
        $headers = Yii::$app->getRequest()->getHeaders();
        $restaurantManager = User::findIdentityByAccessToken(explode(' ', $headers['authorization'])[1]);
        $restaurant = Restaurants::find()->where(['user_id' => $restaurantManager->id])->one();

        if(empty($restaurantManager))
            throw new NotFoundHttpException('User not found');
        if(User::getRoleName($restaurantManager->id) != User::RESTAURANT_MANAGER)
            throw new ForbiddenHttpException('This account is not a restaurant account');
        if(is_null($restaurant))
            throw new ForbiddenHttpException("You don't have permission to do this action");
        if(!$restaurant->status)
            throw new ForbiddenHttpException('This account is deactivated');

        return $restaurant;
    }

    public static function updateRestaurant($data)
    {
        $restaurants = Restaurants::checkRestaurantAccess();
        $model['Restaurants'] = $data;

        $restaurants->load($model);
        $restaurants->validate();

//        if(isset($data['areas'])) {
//
//            if(empty($data['areas']))
//                return Helpers::HttpException(422,'validation failed', ['error' => "areas can't be blank"]);
//
//            $newAreas = $data['areas'];
//            $areaRestaurants = $restaurants->areaRestaurants;
//
//            $models = [];
//            foreach ($areaRestaurants as $AreaRestaurant) {
//                $models[$AreaRestaurant->area_id] = $AreaRestaurant;
//            }
//
//            $areaRestaurants = $models;
//
//            foreach ($newAreas as $Area) {
//
//                if(!isset($Area['id']))
//                    return Helpers::HttpException(422,'validation failed', ['error' => "area id is required"]);
//                if(empty($Area['id']))
//                    return Helpers::HttpException(422,'validation failed', ['error' => "area id can't be blank"]);
//
//                if (!array_key_exists($Area['id'], $areaRestaurants)) {
//
//                    if(empty(MenuItems::getMenuItem($restaurants->id, $Area['id'])))
//                        return Helpers::HttpException(422,'validation failed', ['error' => "There area dos't exist"]);
//
//                    $menuItemChoice = new AreaRestaurant();
//                    $menuItemChoice->area_id = $Area['id'];
//                    $menuItemChoice->restaurant_id = $restaurants->id;
//                    $menuItemChoice->validate();
//                    $menuItemChoice->save();
//                } else {
//                    unset($menuItemChoices[$ItemChoice['id']]);
//                }
//            }
//
//            if (!empty($menuItemChoices))
//                foreach ($menuItemChoices as $MenuItemChoice)
//                    $MenuItemChoice->delete();
//        }


        $isUpdated = $restaurants->save();
        if($isUpdated)
            return Helpers::formatResponse($isUpdated, 'update success', ['id' => $restaurants->id]);
        return Helpers::formatResponse($isUpdated, 'update failed', null);
    }
}
