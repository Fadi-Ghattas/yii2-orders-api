<?php

namespace common\models;

use Yii;
use yii\web\ForbiddenHttpException;
use yii\helpers\Json;
use yii\web\Response;

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
 * @property integer $disable_ordering
 * @property integer $delivery_duration
 * @property string $phone_number
 * @property string $working_hours
 * @property double $longitude
 * @property double $latitude
 * @property string $image
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $owner_id
 * @property integer $user_id
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

//    public $mangerName;
//    public $mangerEmail;
//    public $mangerPassWord;
//    public $ownerName;
//    public $ownerContactNumber;
//    public $ownerEmail;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'restaurants';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'minimum_order_amount', 'time_order_open', 'time_order_close', 'delivery_fee', 'rank', 'halal', 'featured', 'disable_ordering', 'delivery_duration', 'phone_number', 'working_hours', 'longitude', 'latitude', 'status', 'owner_id', 'user_id'], 'required'],
//            [['ownerEmail','ownerContactNumber','ownerName','mangerPassWord','mangerEmail','mangerName','name', 'minimum_order_amount', 'time_order_open', 'time_order_close', 'delivery_fee', 'rank', 'halal', 'featured', 'disable_ordering', 'delivery_duration', 'phone_number', 'working_hours', 'longitude', 'latitude', 'status', 'owner_id', 'user_id'], 'required'],
            [['minimum_order_amount', 'delivery_fee', 'rank', 'longitude', 'latitude'], 'number'],
            [['time_order_open', 'time_order_close', 'created_at', 'updated_at', 'image'], 'safe'],
            [['halal', 'featured', 'disable_ordering', 'delivery_duration', 'status', 'owner_id', 'user_id'], 'integer'],
            [['working_hours'], 'string'],
            [['name', 'phone_number', 'image'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Owners::className(), 'targetAttribute' => ['owner_id' => 'id']],
//            [['ownerEmail','mangerEmail'], 'email'],
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
            'working_hours' => 'Working Hours',
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
            'name',
            'phone_number',
            'minimum_order_amount',
            'time_order_open',
            'time_order_close',
            'delivery_fee',
            'halal',
            'featured',
            'disable_ordering',
            'delivery_duration',
            'working_hours',
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
        $lockedValues = ['name', 'halal', 'featured', 'latitude', 'longitude', 'image', 'status', 'created_at', 'updated_at'];
        foreach ($lockedValues as $lockedValue) {
            if ($this->attributes[$lockedValue] != $this->oldAttributes[$lockedValue])
                throw new ForbiddenHttpException($lockedValue . " can't be changed.");
        }

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function afterValidate(){
        if ($this->hasErrors()) {
            $validation = array();
            $validation['success'] = false;
            $validation['message'] = 'Validation failed.';
            $validation['data'] = $this->errors;

            $response = Yii::$app->getResponse();
            $response->setStatusCode(422);
            $response->format = Response::FORMAT_JSON;
            $response->data = $validation;
            $response->send();
        }
    }
}
