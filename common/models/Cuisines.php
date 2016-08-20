<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cuisines".
 *
 * @property string $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property CuisineRestaurant[] $cuisineRestaurants
 * @property Restaurants[] $restaurants
 */
class Cuisines extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cuisines';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['deleted_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
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
            'deleted_at' => 'Deleted At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCuisineRestaurants()
    {
        return $this->hasMany(CuisineRestaurant::className(), ['cuisine_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRestaurants()
    {
        return $this->hasMany(Restaurants::className(), ['id' => 'restaurant_id'])->viaTable('cuisine_restaurant', ['cuisine_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return CuisinesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CuisinesQuery(get_called_class());
    }
}
