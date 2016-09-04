<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "owners".
 *
 * @property string $id
 * @property string $name
 * @property string $contact_number
 * @property string $email
 *
 * @property Restaurants[] $restaurants
 */
class Owners extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'owners';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'contact_number', 'email'], 'required'],
            [['name', 'contact_number', 'email'], 'string', 'max' => 255],
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
            'contact_number' => 'Contact Number',
            'email' => 'Email',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRestaurants()
    {
        return $this->hasMany(Restaurants::className(), ['owner_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return OwnersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OwnersQuery(get_called_class());
    }

    public function fields()
    {
        $fields=parent::fields();
        unset($fields['id']);
        return $fields;
    }

    public function afterValidate()
    {
        if ($this->hasErrors()) {
            return Helpers::HttpException(422,'validation failed', ['error' => $this->errors]);
        }
    }
}
