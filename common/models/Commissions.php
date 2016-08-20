<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "commissions".
 *
 * @property string $id
 * @property string $from
 * @property string $to
 * @property string $value
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class Commissions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'commissions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from', 'to', 'value'], 'required'],
            [['from', 'to', 'value'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'from' => 'From',
            'to' => 'To',
            'value' => 'Value',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     * @return CommissionsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CommissionsQuery(get_called_class());
    }
}
