<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "setting".
 *
 * @property integer $id
 * @property string $collection
 * @property string $key
 * @property string $value
 */
class Setting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['collection', 'key', 'value'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'collection' => 'Collection',
            'key' => 'Key',
            'value' => 'Value',
        ];
    }

    /**
     * @inheritdoc
     * @return SettingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SettingQuery(get_called_class());
    }

    public static function getSettingValueByName($key){
         return Setting::find()->where(['key' => $key])->one()->value;
    }

    public static function getSettingRecordByName($key){
        return Setting::findOne(['key' => $key]);
    }
    public static function setSettingValueByName($key,$value){
        $model =  Setting::findOne(['key' => $key]);
        $model->value = $value;
        $model->save();
    }
}
