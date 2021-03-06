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

    public static function getOrderCommissions($orderTotal)
    {
        $commissions = self::find()->where(['<=', 'from', $orderTotal])->andWhere(['>', 'to', $orderTotal])->one();
        if (empty($commissions))
            $commissions = self::find()->orderBy('value DESC')->one();
        return $commissions;
    }
}
