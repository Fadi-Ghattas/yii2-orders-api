<?php

namespace common\models;


use Yii;
use common\helpers\Helpers;
/**
 * This is the model class for table "item_choices".
 *
 * @property string $id
 * @property string $name
 * @property integer $status
 * @property string $restaurant_id
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Restaurants $restaurant
 * @property MenuItemChoice[] $menuItemChoices
 * @property MenuItems[] $menuItems
 * @property OrderItems[] $orderItems
 */
class ItemChoices extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'item_choices';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'status', 'restaurant_id'], 'required'],
            [['status', 'restaurant_id'], 'integer'],
            [['status'], 'boolean', 'trueValue' => 1, 'falseValue' => 0],
            ['status', 'in', 'range' => [0, 1]],
            [['deleted_at', 'created_at', 'updated_at'], 'safe'],
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
            'status' => 'Status',
            'restaurant_id' => 'Restaurant ID',
            'deleted_at' => 'Deleted At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
    public function getMenuItemChoices()
    {
        return $this->hasMany(MenuItemChoice::className(), ['choice_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuItems()
    {
        return $this->hasMany(MenuItems::className(), ['id' => 'menu_item_id'])->viaTable('menu_item_choice', ['choice_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItems::className(), ['choice_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return ItemChoicesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ItemChoicesQuery(get_called_class());
    }

    public static function getItemChoice($item_choice_id)
    {
        return ItemChoices::find()->where(['id' => $item_choice_id])->andWhere(['deleted_at' => null])->one();
    }

    public static function getRestaurantItemsChoices()
    {
        $restaurant = Restaurants::checkRestaurantAccess();
        if (empty($restaurant->itemChoices))
            return Helpers::formatResponse(false, 'get failed', ['error' => "restaurant has no items of choices"]);

        return Helpers::formatResponse(true, 'get success', $restaurant->itemChoices);
    }

    public static function getRestaurantItemChoice($item_choice_id)
    {
        $restaurant = Restaurants::checkRestaurantAccess();
        $itemChoice = self::getItemChoice($item_choice_id);
        if (empty($itemChoice))
            return Helpers::formatResponse(false, 'get failed', ['error' => "this item of choices dos't exist"]);

        return Helpers::formatResponse(false, 'get success', $itemChoice);
    }

    public static function createItemChoice($data)
    {
        $restaurant = Restaurants::checkRestaurantAccess();

        if (!isset($data['name']))
            return Helpers::UnprocessableEntityHttpException('validation failed', ['error' => 'name is required']);

        $ItemChoice = new ItemChoices();
        $ItemChoice->name = $data['name'];
        $ItemChoice->status = 1;
        $ItemChoice->restaurant_id = $restaurant->id;
        $isCreated = $ItemChoice->save();
        if (!$isCreated)
            return Helpers::formatResponse($isCreated, 'create failed', null);
        return Helpers::formatResponse($isCreated, 'create success', ['id' => $ItemChoice->id]);
    }

    public static function updateItemChoice($item_choice_id, $data)
    {
        $restaurant = Restaurants::checkRestaurantAccess();

        $ItemChoice = self::getItemChoice($item_choice_id);

        if (is_null($ItemChoice))
            return Helpers::UnprocessableEntityHttpException('validation failed', ['error' => "This Item of Choices dos't exist"]);

        if ($ItemChoice->restaurant_id != $restaurant->id)
            throw new ForbiddenHttpException("You don't have permission to do this action");

        foreach ($data as $DataKey => $DataValue) {
            if (array_key_exists($DataKey, $ItemChoice->oldAttributes)) {
                $ItemChoice->$DataKey = $DataValue;
            }
        }

        $isUpdated = $ItemChoice->save();
        if (!$isUpdated)
            return Helpers::formatResponse($isUpdated, 'update failed', null);

        return Helpers::formatResponse($isUpdated, 'update success', ['id' => $ItemChoice->id]);
    }

    public static function deleteItemChoice($item_choice_id)
    {
        $restaurant = Restaurants::checkRestaurantAccess();

        $ItemChoice = self::getItemChoice($item_choice_id);

        if (is_null($ItemChoice))
            return $ItemChoice::UnprocessableEntityHttpException('validation failed', ['error' => "This item choices dos't exist"]);

        if ($ItemChoice->restaurant_id != $restaurant->id)
            throw new ForbiddenHttpException("You don't have permission to do this action");

        $ItemChoice->deleted_at = date('Y-m-d H:i:s');
        $isUpdated = $ItemChoice->save();

        if (!$isUpdated)
            return Helpers::formatResponse($isUpdated, 'deleted failed', null);

        return Helpers::formatResponse($isUpdated, 'deleted success', ['id' => $ItemChoice->id]);
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
            'status',
        ];

    }
}
