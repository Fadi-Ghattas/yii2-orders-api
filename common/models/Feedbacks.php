<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "feedbacks".
 *
 * @property string $id
 * @property string $comment
 * @property string $client_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property Clients $client
 */
class Feedbacks extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'feedbacks';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment', 'client_id'], 'required'],
            [['comment'], 'string'],
            [['client_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clients::className(), 'targetAttribute' => ['client_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'comment' => 'Comment',
            'client_id' => 'Client ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Clients::className(), ['id' => 'client_id']);
    }

    /**
     * @inheritdoc
     * @return FeedbacksQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new FeedbacksQuery(get_called_class());
    }
}
