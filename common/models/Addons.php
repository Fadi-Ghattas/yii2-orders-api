<?php

namespace common\models;


use Yii;
use common\helpers\Helpers;

/**
 * This is the model class for table "addons".
 *
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string $price
 * @property integer $status
 * @property string $image
 * @property string $restaurant_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property Restaurants $restaurant
 * @property MenuItemAddon[] $menuItemAddons
 * @property MenuItems[] $menuItems
 */
class Addons extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'addons';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description', 'price', 'status', 'restaurant_id'], 'required'],
            [['price'], 'number'],
            [['status', 'restaurant_id'], 'integer'],
            [['status'], 'boolean', 'trueValue' => 1, 'falseValue' => 0],
            ['status', 'in', 'range' => [0, 1]],
            [['name', 'description', 'image'], 'string', 'max' => 255],
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
            'description' => 'Description',
            'price' => 'Price',
            'status' => 'Status',
            'image' => 'Image',
            'restaurant_id' => 'Restaurant ID',
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
    public function getMenuItemAddons()
    {
        return $this->hasMany(MenuItemAddon::className(), ['addon_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuItems()
    {
        return $this->hasMany(MenuItems::className(), ['id' => 'menu_item_id'])->viaTable('menu_item_addon', ['addon_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return AddonsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AddonsQuery(get_called_class());
    }

    public static function getAddOn($add_on_id)
    {
        return Addons::find()->where(['id' => $add_on_id])->andWhere(['deleted_at' => null])->one();
    }

    public static function getRestaurantAddOns()
    {
        $restaurant = Restaurants::checkRestaurantAccess();
        if (empty($restaurant->addons))
            return Helpers::formatResponse(false, 'get failed', ['error' => "restaurant has no add-on's"]);

        return Helpers::formatResponse(true, 'get success', $restaurant->addons);
    }

    public static function getRestaurantAddOn($add_on_id)
    {
        $restaurant = Restaurants::checkRestaurantAccess();
        $add_on = self::getAddOn($add_on_id);
        if (empty($add_on))
            return Helpers::formatResponse(false, 'get failed', ['error' => "this add-on dos't exist"]);

        return Helpers::formatResponse(false, 'get success', $add_on);
    }

    public static function createAddOn($data)
    {
        $restaurant = Restaurants::checkRestaurantAccess();

        if (!isset($data['name']))
            return Helpers::UnprocessableEntityHttpException('validation failed', ['error' => 'name is required']);

        $addOn = new Addons();
        $addOn->name = $data['name'];
        $addOn->description = (isset($data['description']) ? $data['description'] : 'no description');
        $addOn->price = (isset($data['price']) ? $data['price'] : 0.00);
        $addOn->status = 1;
        $addOn->restaurant_id = $restaurant->id;
        $isCreated = $addOn->save();
        if (!$isCreated)
            return Helpers::formatResponse($isCreated, 'create failed', null);
        return Helpers::formatResponse($isCreated, 'create success', ['id' => $addOn->id]);
    }

    public static function updateAddOn($add_on_id, $data)
    {
        $restaurant = Restaurants::checkRestaurantAccess();

        $addOn = self::getAddOn($add_on_id);

        if (is_null($addOn))
            return Helpers::UnprocessableEntityHttpException('validation failed', ['error' => "This add-on dos't exist"]);

        if ($addOn->restaurant_id != $restaurant->id)
            throw new ForbiddenHttpException("You don't have permission to do this action");

        foreach ($data as $DataKey => $DataValue) {
            if (array_key_exists($DataKey, $addOn->oldAttributes)) {
                $addOn->$DataKey = $DataValue;
            }
        }

        $isUpdated = $addOn->save();
        if (!$isUpdated)
            return Helpers::formatResponse($isUpdated, 'update failed', null);

        return Helpers::formatResponse($isUpdated, 'update success', ['id' => $addOn->id]);
    }

    public static function deleteAddOn($add_on_id)
    {
        $restaurant = Restaurants::checkRestaurantAccess();

        $addOn = self::getAddOn($add_on_id);

        if (is_null($addOn))
            return Helpers::UnprocessableEntityHttpException('validation failed', ['error' => "This add-on dos't exist"]);

        if ($addOn->restaurant_id != $restaurant->id)
            throw new ForbiddenHttpException("You don't have permission to do this action");

        $addOn->deleted_at = date('Y-m-d H:i:s');
        $isUpdated = $addOn->save();

        if (!$isUpdated)
            return Helpers::formatResponse($isUpdated, 'deleted failed', null);

        return Helpers::formatResponse($isUpdated, 'deleted success', ['id' => $addOn->id]);
    }

    public function afterValidate()
    {
        if ($this->hasErrors()) {
            Helpers::UnprocessableEntityHttpException('validation failed', ['error' => $this->errors]);
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
        return [
            'id',
            'name',
            'description',
            'price',
            'status',
        ];

    }
}
