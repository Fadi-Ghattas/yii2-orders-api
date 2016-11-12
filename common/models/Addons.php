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
            [['name', 'status', 'restaurant_id'], 'required'],
            [['price'], 'number'],
            [['restaurant_id'], 'integer'],
            [['status'], 'boolean'],
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

    public static function getAddOn($restaurant_id, $add_on_id)
    {
        return self::find()->where(['restaurant_id' => $restaurant_id])->andWhere(['id' => $add_on_id])->andWhere(['deleted_at' => null])->one();
    }

    public static function getAddOnByName($restaurant_id, $add_on_name)
    {
        return self::find()->where(['restaurant_id' => $restaurant_id])->andWhere(['name' => $add_on_name])->andWhere(['deleted_at' => null])->one();
    }

    public static function getRestaurantAddOns()
    {
        $restaurant = Restaurants::checkRestaurantAccess();
        return Helpers::formatResponse(true, 'get success', $restaurant->addons);
    }

    public static function getRestaurantAddOn($add_on_id)
    {
        $restaurant = Restaurants::checkRestaurantAccess();
        $add_on = self::getAddOn($restaurant->id, $add_on_id);
        if (empty($add_on))
            return Helpers::HttpException(404, 'get failed', ['error' => "this add-on dos't exist"]);

        return Helpers::formatResponse(true, 'get success', $add_on);
    }

    public static function createAddOn($data)
    {
        $restaurant = Restaurants::checkRestaurantAccess();

        $addOn = new Addons();
        $model['Addons'] = $data;
        $addOn->load($model);
        $addOn->status = 1;
        $addOn->restaurant_id = $restaurant->id;
        $addOn->validate();

        if (!empty(self::getAddOnByName($restaurant->id, $data['name'])))
            return Helpers::HttpException(409, 'name conflict', ['error' => 'There is already add-on with the same name']);

        $isCreated = $addOn->save();
        if (!$isCreated)
            return Helpers::HttpException(422, 'create failed', null);
        return Helpers::formatResponse(true, 'create success', ['id' => $addOn->id]);
    }

    public static function updateAddOn($add_on_id, $data)
    {
        $restaurant = Restaurants::checkRestaurantAccess();

        $addOn = self::getAddOn($restaurant->id, $add_on_id);

        if (empty($addOn))
            return Helpers::HttpException(404, 'update failed', ['error' => "This add-on dos't exist"]);

        $model['Addons'] = $data;
        $addOn->load($model);
        $addOn->validate();

        if (isset($data['name'])) {
            //check restaurant add-on name if is it unique before update
            $CheckUniqueAddOn = self::getAddOnByName($restaurant->id, $data['name']);
            if (!empty($CheckUniqueAddOn) && $CheckUniqueAddOn->id != $add_on_id)
                return Helpers::HttpException(409, 'name conflict', ['error' => 'There is already AddOn with the same name']);
        }

        $isUpdated = $addOn->save();
        if (!$isUpdated)
            return Helpers::HttpException(422, 'update failed', null);

        return Helpers::formatResponse(true, 'update success', ['id' => $addOn->id]);
    }

    public static function deleteAddOn($add_on_id)
    {
        $restaurant = Restaurants::checkRestaurantAccess();

        $addOn = self::getAddOn($restaurant->id, $add_on_id);

        if (empty($addOn))
            return Helpers::HttpException(404, 'deleted failed', ['error' => "This add-on dos't exist"]);

        $addOn->deleted_at = date('Y-m-d H:i:s');
        $isUpdated = $addOn->save();

        if (!$isUpdated)
            return Helpers::HttpException(422, 'deleted failed', null);

        return Helpers::formatResponse(true, 'deleted success', ['id' => $addOn->id]);
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

    public function fields()
    {
        return [
            'id' => function () {
                return (int)$this->id;
            },
            'name' => function () {
                return (string)$this->name;
            },
            'description' => function () {
                return (!empty($this->description) ? (string)$this->description : null);
            },
            'price' => function () {
                return (float)$this->price;
            },
            'status' => function () {
                return (bool)$this->status;
            },
        ];
    }
}
