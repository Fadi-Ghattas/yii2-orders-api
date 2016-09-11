<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cuisines".
 *
 * @property string $id
 * @property string $name
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property string $image
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
            [['deleted_at', 'created_at', 'updated_at'], 'safe'],
            [['name', 'image'], 'string', 'max' => 255],
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
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'image' => 'Image',
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

    public function fields()
    {
        return ['id','name'];
    }

    public function afterValidate()
    {
        if ($this->hasErrors()) {
            return Helpers::HttpException(422,'validation failed', ['error' => $this->errors]);
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
}
