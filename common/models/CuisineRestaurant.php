<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cuisine_restaurant".
 *
 * @property string $cuisine_id
 * @property string $restaurant_id
 *
 * @property Cuisines $cuisine
 * @property Restaurants $restaurant
 */
class CuisineRestaurant extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cuisine_restaurant';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cuisine_id', 'restaurant_id'], 'required'],
            [['cuisine_id', 'restaurant_id'], 'integer'],
            [['cuisine_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cuisines::className(), 'targetAttribute' => ['cuisine_id' => 'id']],
            [['restaurant_id'], 'exist', 'skipOnError' => true, 'targetClass' => Restaurants::className(), 'targetAttribute' => ['restaurant_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cuisine_id' => 'Cuisine ID',
            'restaurant_id' => 'Restaurant ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCuisine()
    {
        return $this->hasOne(Cuisines::className(), ['id' => 'cuisine_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRestaurant()
    {
        return $this->hasOne(Restaurants::className(), ['id' => 'restaurant_id']);
    }

    /**
     * @inheritdoc
     * @return CuisineRestaurantQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CuisineRestaurantQuery(get_called_class());
    }

    public function afterValidate()
    {
        if ($this->hasErrors()) {
            return Helpers::HttpException(422,'validation failed', ['error' => $this->errors]);
        }
    }
}
