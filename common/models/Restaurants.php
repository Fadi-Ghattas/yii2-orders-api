<?php

namespace common\models;


use Yii;
use yii\helpers\ArrayHelper;
use common\helpers\Helpers;
use yii\i18n\Formatter;

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
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $user_id
 * @property string $logout_at
 * @property string $image_background
 * @property integer $is_verified_global
 * @property string $country_id
 * @property string $owner_number
 * @property string $res_status
 * @property double $reviews_rank
 * @property integer $favour_it
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
 * @property Countries $country
 * @property Reviews[] $reviews
 */
class Restaurants extends \yii\db\ActiveRecord
{
    public $action;
    public $res_status;
    public $reviews_rank;
    public $favour_it;
    const SCENARIO_GET_BY_RESTAURANTS_MANGER = 'get_by_restaurants_manger';
    const SCENARIO_GET_BY_CLIENT = 'get_by_client';

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
            [['name', 'minimum_order_amount', 'time_order_open', 'time_order_close', 'delivery_fee', 'rank', 'halal', 'featured', 'working_opening_hours', 'working_closing_hours', 'disable_ordering', 'delivery_duration', 'phone_number', 'owner_number', 'country_id', 'longitude', 'latitude', 'image', 'status', 'user_id'], 'required'],
            [['minimum_order_amount', 'delivery_fee', 'rank', 'longitude', 'latitude'], 'number'],
            [['res_status', 'reviews_rank', 'favour_it', 'action', 'time_order_open', 'time_order_close', 'working_opening_hours', 'working_closing_hours', 'created_at', 'updated_at'], 'safe'],
            [['halal', 'featured', 'disable_ordering', 'delivery_duration', 'status', 'user_id', 'is_verified_global', 'country_id'], 'integer'],
            [['name', 'phone_number', 'owner_number', 'image', 'image_background'], 'string', 'max' => 255],
            [['working_opening_hours', 'working_closing_hours', 'time_order_open', 'time_order_close'], 'date', 'format' => 'H:m:s'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Countries::className(), 'targetAttribute' => ['country_id' => 'id']],
//            [['phone_number'],  'udokmeci\yii2PhoneValidator\PhoneValidator','country'=> 'MY', 'strict'=>false],
//            [['owner_number'], 'number', 'numberPattern' => '/^\s*[-+]?[0-9]*[.,]?[0-9]+([eE][-+]?[0-9]+)?\s*$/'],
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
            'working_opening_hours' => 'Working Opening Hours',
            'working_closing_hours' => 'Working Closing Hours',
            'disable_ordering' => 'Disable Ordering',
            'delivery_duration' => 'Delivery Duration',
            'phone_number' => 'Phone Number',
            'longitude' => 'Longitude',
            'latitude' => 'Latitude',
            'image' => 'Image',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'user_id' => 'User ID',
            'logout_at' => 'Logout At',
            'image_background' => 'Image Background',
            'is_verified_global' => 'Is Verified Global',
            'country_id' => 'Country ID',
            'owner_number' => 'Owner Number',
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
        return $this->hasMany(PaymentMethodRestaurant::className(), ['restaurant_id' => 'id'])->joinWith(['paymentMethod']);
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
    public function getCountry()
    {
        return $this->hasOne(Countries::className(), ['id' => 'country_id']);
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

    public function scenarios()
    {
        return ArrayHelper::merge(
            parent::scenarios(),
            [
                self::SCENARIO_GET_BY_RESTAURANTS_MANGER => [
                    'id',
                    'name',
                    'email' => function () {
                        return $this->user->email;
                    },
                    'phone_number',
                    'owner_number',
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
                    'country_id',
                    'areas' => function () {
                        return $this->areas;
                    },
                    'cuisine' => function () {
                        return $this->cuisines;
                    },
                    'payment_method' => function () {
                        $paymentMethodRestaurants = array();
                        foreach ($this->paymentMethodRestaurants as $payment_method) {
                            $single_payment_method = array();
                            $single_payment_method['id'] = $payment_method->paymentMethod->id;
                            $single_payment_method['name'] = $payment_method->paymentMethod->name;
                            $paymentMethodRestaurants [] = $single_payment_method;
                        }
                        return $paymentMethodRestaurants;
                    }
                ],

                self::SCENARIO_GET_BY_CLIENT => [
                    'id',
                    'name',
                    'image',
                    'minimum_order_amount',
                    'delivery_duration',
                    'res_status',
                    'reviews_rank',
                    'favour_it',
                    'halal',
                    'cuisine' => function () {
                        return $this->cuisines;
                    },
                ],

            ]);
    }

    public function fields()
    {
        $request_action = explode('/', Yii::$app->getRequest()->getUrl());
        if (in_array('clients', $request_action) && Yii::$app->request->isGet)
            return $this->scenarios()[self::SCENARIO_GET_BY_CLIENT];
        return $this->scenarios()[self::SCENARIO_GET_BY_RESTAURANTS_MANGER];
    }

    public function afterFind()
    {
//        if (!$this->status)
//            return Helpers::HttpException(403, "This account is deactivated", null);

        parent::afterFind(); // TODO: Change the autogenerated stub
    }

    public function beforeSave($insert)
    {
        $post_data = Yii::$app->request->post();
        if (empty($post_data))
            return Helpers::HttpException(422, 'validation failed', ['error' => ['please provide data']]);

        $headers = Yii::$app->getRequest()->getHeaders();
        $auth_key = explode(' ', $headers['authorization'])[1];
        $restaurantManager = User::findIdentityByAccessToken($auth_key);
        if (empty($restaurantManager))
            return Helpers::HttpException(404, 'failed', ['error' => 'user not found']);
        if (User::getRoleName($restaurantManager->id) != User::RESTAURANT_MANAGER)
            return Helpers::HttpException(403, "forbidden", ['error' => "This account is not a restaurant account"]);
        if ($this->user_id != $restaurantManager->id)
            return Helpers::HttpException(403, "forbidden", ['error' => "You don't have permission to do this action"]);
        if (!$this->oldAttributes['status'])
            return Helpers::HttpException(403, "forbidden", ['error' => "This account is deactivated"]);

        $lockedValues = ['name', 'halal', 'rank', 'featured', 'latitude', 'longitude', 'image', 'status', 'created_at', 'updated_at'];

        if (!$this->action == 'logout')
            $lockedValues [] = 'email';

        foreach ($post_data as $lockedValueKey => $lockedValue) {
            if (in_array($lockedValueKey, $lockedValues))
                return Helpers::HttpException(403, "forbidden", ['error' => $lockedValueKey . " can't be changed"]);
        }

        if (!$this->isNewRecord)
            $this->updated_at = date('Y-m-d H:i:s');
        else
            $this->created_at = date('Y-m-d H:i:s');

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function afterValidate()
    {
        if ($this->hasErrors()) {
            return Helpers::HttpException(422, 'validation failed', ['error' => $this->errors]);
        }
    }

    public static function checkRestaurantAccess()
    {
        $headers = Yii::$app->getRequest()->getHeaders();
        $restaurantManager = User::findIdentityByAccessToken(explode(' ', $headers['authorization'])[1]);
        $restaurant = Restaurants::find()->where(['user_id' => $restaurantManager->id])->one();

        if (empty($restaurantManager))
            return Helpers::HttpException(404, 'failed', ['error' => 'user not found']);
        if (User::getRoleName($restaurantManager->id) != User::RESTAURANT_MANAGER)
            return Helpers::HttpException(403, "forbidden", ['error' => "This account is not a restaurant account"]);
        if (is_null($restaurant))
            return Helpers::HttpException(403, "forbidden", ['error' => "You don't have permission to do this action"]);
        if (!$restaurant->status)
            return Helpers::HttpException(403, "forbidden", ['error' => "This account is deactivated"]);

        return $restaurant;
    }

    public static function updateRestaurant($data)
    {
        $restaurants = Restaurants::checkRestaurantAccess();
        $model['Restaurants'] = $data;

        $restaurants->load($model);
        $restaurants->validate();

        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {

            $restaurants->save();

            if (isset($data['areas'])) {

                if (empty($data['areas']))
                    return Helpers::HttpException(422, 'validation failed', ['error' => "areas can't be blank"]);

                $newAreas = $data['areas'];
                $areaRestaurants = $restaurants->areaRestaurants;

                $models = [];
                foreach ($areaRestaurants as $AreaRestaurant) {
                    $models[$AreaRestaurant->area_id] = $AreaRestaurant;
                }

                $areaRestaurants = $models;

                foreach ($newAreas as $Area) {

                    if (!isset($Area['id']))
                        return Helpers::HttpException(422, 'validation failed', ['error' => "area id is required"]);
                    if (empty($Area['id']))
                        return Helpers::HttpException(422, 'validation failed', ['error' => "area id can't be blank"]);

                    if (!array_key_exists($Area['id'], $areaRestaurants)) {

                        if (empty(MenuItems::getMenuItem($restaurants->id, $Area['id'])))
                            return Helpers::HttpException(404, 'update failed', ['error' => "There area dos't exist"]);

                        $areaRestaurant = new AreaRestaurant();
                        $areaRestaurant->area_id = $Area['id'];
                        $areaRestaurant->restaurant_id = $restaurants->id;
                        $areaRestaurant->validate();
                        $areaRestaurant->save();
                    } else {
                        unset($areaRestaurants[$Area['id']]);
                    }
                }

                if (!empty($areaRestaurants))
                    foreach ($areaRestaurants as $AreaRestaurant)
                        $AreaRestaurant->delete();
            }

            $transaction->commit();
            return Helpers::formatResponse(true, 'update success', ['id' => $restaurants->id]);
        } catch (\Exception $e) {
            $transaction->rollBack();
            return Helpers::HttpException(422, 'update failed', null);
            //throw $e;
        }
        return Helpers::HttpException(422, 'update failed', null);
    }

    public static function getRestaurants()
    {
        $get_data = Yii::$app->request->get();
        $page = 1;
        $limit = -1;

        if (!isset($get_data['area']) && empty(trim($get_data['area'])))
            return Helpers::HttpException(422, 'validation failed', ['error' => "area is required and can't be blank"]);
        $area_id = trim($get_data['area']);
        $AreaCountry = Areas::find()->where(['areas.id' => $area_id])->joinWith(['state', 'state.country'], true, 'INNER JOIN')->select('countries.name')->one();
        $time = (new Formatter(['timeZone' => Helpers::getCountryTimeZone($AreaCountry->name)]))->asTime(time(), 'php:H:i:s');


        $headers = Yii::$app->getRequest()->getHeaders();
        $client_id = 0;
        if (isset($headers['authorization'])) {
            if (empty($headers['authorization']))
                return Helpers::HttpException(422, 'validation failed', ['error' => "authorization can't be blank"]);
            $authorization = explode(' ', $headers['authorization'])[1];
            $ClientUser = User::findIdentityByAccessToken($authorization);
            if (empty($ClientUser))
                return Helpers::HttpException(404, 'not found', ['error' => 'client not found']);
            $client_id = Clients::findOne(['user_id' => $ClientUser->id])->id;
            if (empty($client_id))
                return Helpers::HttpException(404, 'not found', ['error' => 'client not found']);
//            $client_id = $client_id->id;
        }

        //1 open and delivery open
        //2 open but no delivery
        //3 busy
        //4 open but delivery closed
        //5 open
        //6 closed
        $sql = "SELECT r.* 
               FROM (SELECT *, 
                       (
                        CASE 
                        WHEN ('" . $time . "' >= restaurants.time_order_open AND '" . $time . "' <= restaurants.time_order_close AND restaurants.disable_ordering = 1) THEN 3
                        WHEN ('" . $time . "' >= restaurants.working_opening_hours AND '" . $time . "' < restaurants.time_order_open) THEN 2
                        WHEN ('" . $time . "' >= restaurants.time_order_open AND '" . $time . "' <= restaurants.time_order_close) THEN 1  
                        WHEN ('" . $time . "' >= restaurants.time_order_close AND '" . $time . "' <= restaurants.working_closing_hours) THEN 4
                        WHEN ('" . $time . "' >= restaurants.working_opening_hours AND '" . $time . "' <= restaurants.working_closing_hours) THEN 5
                        WHEN ('" . $time . "' > restaurants.working_closing_hours OR '" . $time . "' < restaurants.working_opening_hours) THEN 6
                        ELSE NULL END
                       ) AS 'res_status',
                       (SELECT ROUND(AVG(reviews.rank), 1) FROM reviews WHERE reviews.restaurant_id = restaurants.id) AS 'reviews_rank',
                       (
                        SELECT EXISTS(SELECT favorite_restaurants.id 
                                      FROM favorite_restaurants 
                                      WHERE favorite_restaurants.restaurant_id = restaurants.id AND favorite_restaurants.client_id = " . $client_id . ") 
                       ) AS 'favour_it'
                       FROM `restaurants`) AS r
                       JOIN countries ON r.country_id = countries.id
                       JOIN states ON countries.id = states.country_id
                       JOIN areas ON states.id = areas.state_id
               WHERE areas.id IN (" . $area_id . ") AND ( ";

        $addOr = 0;
        if (isset($get_data['minimum_order_amount'])) {
            $minimum_order_amount = trim($get_data['minimum_order_amount']);
            if (empty($minimum_order_amount))
                return Helpers::HttpException(422, 'validation failed', ['error' => "minimum_order_amount can't be blank"]);
            $MinimumOrderAmountRang = explode(',', $minimum_order_amount);
            if (count($MinimumOrderAmountRang) != 2)
                return Helpers::HttpException(422, 'validation failed', ['error' => "minimum_order_amount rang must have two values"]);
            if (!is_double(doubleval($MinimumOrderAmountRang[0])) && !is_double(doubleval($MinimumOrderAmountRang[1])))
                return Helpers::HttpException(422, 'validation failed', ['error' => "minimum_order_amount rang values must be numbers"]);
            $sql .= ' r.minimum_order_amount BETWEEN ' . $MinimumOrderAmountRang[0] . ' AND ' . $MinimumOrderAmountRang[1] . ' ';
            $addOr = 1;
        }

        if (isset($get_data['delivery_fee'])) {
            $delivery_fee = trim($get_data['delivery_fee']);
            if (empty($delivery_fee))
                return Helpers::HttpException(422, 'validation failed', ['error' => "delivery_fee can't be blank"]);
            $DeliveryFeeRang = explode(',', $delivery_fee);
            if (count($DeliveryFeeRang) != 2)
                return Helpers::HttpException(422, 'validation failed', ['error' => "delivery_fee rang must have two values"]);
            if (!is_double(doubleval($DeliveryFeeRang[0])) && !is_double(doubleval($DeliveryFeeRang[1])))
                return Helpers::HttpException(422, 'validation failed', ['error' => "delivery_fee rang values must be numbers"]);
            if ($addOr) {
                $sql .= ' OR r.delivery_fee BETWEEN ' . $DeliveryFeeRang[0] . ' AND ' . $DeliveryFeeRang[1] . ' ';
            } else {
                $sql .= ' r.delivery_fee BETWEEN ' . $DeliveryFeeRang[0] . ' AND ' . $DeliveryFeeRang[1] . ' ';
                $addOr = 1;
            }
        }

        if (isset($get_data['delivery_duration'])) {
            $delivery_duration = trim($get_data['delivery_duration']);
            if (empty($delivery_duration))
                return Helpers::HttpException(422, 'validation failed', ['error' => "delivery_duration can't be blank"]);
            if (!is_int(intval($delivery_duration)))
                return Helpers::HttpException(422, 'validation failed', ['error' => "delivery_duration must be a number"]);
            if ($addOr) {
                $sql .= ' OR r.delivery_duration <= ' . $delivery_duration . ' ';
            } else {
                $sql .= ' r.delivery_duration <= ' . $delivery_duration . ' ';
                $addOr = 1;
            }
        }

        if (isset($get_data['quick_filters'])) {
            if (empty($get_data['quick_filters']))
                return Helpers::HttpException(422, 'validation failed', ['error' => "quick_filters can't be blank"]);
            $quick_filters = explode(',', $get_data['quick_filters']);

            if (in_array('open', $quick_filters)) {
                if ($addOr) {
                    $sql .= ' OR r.res_status = 1 ';
                } else {
                    $sql .= ' r.res_status = 1 ';
                    $addOr = 1;
                }
            }

            if (in_array('free_delivery', $quick_filters)) {
                if ($addOr) {
                    $sql .= ' OR r.delivery_fee = 0 ';
                } else {
                    $sql .= ' r.delivery_fee = 0 ';
                    $addOr = 1;
                }
            }

            if (in_array('halal', $quick_filters)) {
                if ($addOr) {
                    $sql .= ' OR r.halal = 1 ';
                } else {
                    $sql .= ' r.halal = 1 ';
                    $addOr = 1;
                }
            }

            if (in_array('accepts_vouchers', $quick_filters)) {
                if ($addOr) {
                    $sql .= ' OR r.accepts_vouchers = 1 ';
                } else {
                    $sql .= ' r.accepts_vouchers = 1 ';
                    $addOr = 1;
                }
            }
            if (in_array('new_restaurant', $quick_filters)) {
                if ($addOr) {
                    $sql .= ' OR TIMESTAMPDIFF(MONTH, r.created_at ,CURDATE()) <= 1 ';
                } else {
                    $sql .= ' TIMESTAMPDIFF(MONTH, r.created_at ,CURDATE()) <= 1 ';
                    $addOr = 1;
                }
            }
        }

        if ($addOr)
            $sql .= ' ) ';
        else
            $sql .= ' 1 ) ';

        if (isset($get_data['sort'])) {
            $sort = trim($get_data['sort']);
            if (empty($sort))
                return Helpers::HttpException(422, 'validation failed', ['error' => "sort can't be blank"]);

            $sort = Helpers::split_on($sort, 1);

            if ($sort[0] != '*' && $sort[0] != '-')
                return Helpers::HttpException(422, 'validation failed', ['error' => "sorting operator is required and can't be " . $sort[0]]);

            $sql .= ' ORDER BY r.' . $sort[1] . ($sort[0] == '*' ? ' ASC ' : ' DESC ');
        } else {
            $sql .= ' ORDER BY r.res_status ASC,  r.delivery_fee ASC, r.delivery_duration ASC ';
        }

        if (isset($get_data['limit']) && isset($get_data['page'])) {
            Helpers::validateSetEmpty([$get_data['page'], $get_data['limit']]);
            if (is_int(intval(trim($get_data['page']))) && is_int(intval(trim($get_data['limit'])))) {
                $page = intval(trim($get_data['page']));
                $limit = intval(trim($get_data['limit']));
            } else {
                return Helpers::HttpException(422, 'validation failed', ['error' => 'page and limit must be integer']);
            }
        }

        if ($limit != -1)
            $sql .= ' LIMIT ' . ($page - 1) . ' , ' . $limit . ';';

        $restaurants = Restaurants::findBySql($sql)->all();
        return Helpers::formatResponse(true, 'get success', $restaurants);
    }

    public static function getRestaurantsStatus($statusId)
    {
        switch ($statusId) {
            case 1:
                return 'open and delivery open';
            case 2:
                return 'open but no delivery';
            case 3:
                return 'busy';
            case 4:
                return 'open but delivery closed';
            case 5:
                return 'open';
            case 6:
                return 'closed';
            default:
                return false;
        }
    }
}
