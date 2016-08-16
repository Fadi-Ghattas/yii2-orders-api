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
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $owner_id
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
            [['name', 'minimum_order_amount', 'time_order_open', 'time_order_close', 'delivery_fee', 'rank', 'halal', 'featured', 'working_opening_hours', 'working_closing_hours', 'disable_ordering', 'delivery_duration', 'phone_number', 'longitude', 'latitude', 'image', 'status', 'owner_id', 'user_id'], 'required'],
            [['minimum_order_amount', 'delivery_fee', 'rank', 'longitude', 'latitude'], 'number'],
            [['time_order_open', 'time_order_close', 'working_opening_hours', 'working_closing_hours', 'created_at', 'updated_at'], 'safe'],
            [['halal', 'featured', 'disable_ordering', 'delivery_duration', 'status', 'owner_id', 'user_id'], 'integer'],
            [['name', 'phone_number', 'image'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Owners::className(), 'targetAttribute' => ['owner_id' => 'id']],
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
            'owner_id' => 'Owner ID',
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
        return $this->hasMany(Addons::className(), ['restaurant_id' => 'id']);
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
        return $this->hasMany(Areas::className(), ['id' => 'area_id'])->viaTable('area_restaurant', ['restaurant_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBlacklistedClients()
    {
        return $this->hasMany(BlacklistedClients::className(), ['restaurant_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCuisineRestaurants()
    {
        return $this->hasMany(CuisineRestaurant::className(), ['restaurant_id' => 'id']);
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
        return $this->hasMany(ItemChoices::className(), ['restaurant_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuCategories()
    {
        return $this->hasMany(MenuCategories::className(), ['restaurant_id' => 'id']);
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
    public function getOwner()
    {
        return $this->hasOne(Owners::className(), ['id' => 'owner_id']);
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
                $user = User::findOne($this->user_id);
                return $user['email'];
            },
            'phone_number',
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
            'owner' => function () {
                $owner = Owners::findOne($this->owner_id);
                return $owner;
            }
        ];
    }

    public function afterFind()
    {
        parent::afterFind(); // TODO: Change the autogenerated stub
    }

    public function beforeSave($insert)
    {
        $post_data = Yii::$app->request->post();

        if(empty($post_data))
            Helpers::UnprocessableEntityHttpException('validation failed', ['data' => ['please provide data']]);

        if(isset($post_data['email'])) {
            $user = User::find()->where(['id' => $this->user_id])->one();
//            $user = new User();
            $user->email = $post_data['email'];
//            $user->username = $post_data['email'];
            $user->save();
        }

        $headers = Yii::$app->getRequest()->getHeaders();
        $auth_key = explode(' ',$headers['authorization'])[1];
        $restaurantManager = \common\models\User::findIdentityByAccessToken($auth_key);
        if($this->user_id != $restaurantManager->id){
            throw new ForbiddenHttpException("You don't have permission to do this action");
        }

        $lockedValues = ['name', 'halal', 'featured', 'latitude', 'longitude', 'image', 'status', 'created_at', 'updated_at'];
        foreach ($lockedValues as $lockedValue) {
            if ($this->attributes[$lockedValue] != $this->oldAttributes[$lockedValue])
                throw new ForbiddenHttpException($lockedValue . " can't be changed.");
        }

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function afterValidate(){
        if ($this->hasErrors()) {
            Helpers::UnprocessableEntityHttpException('validation failed' ,  $this->errors);
        }
    }


}
