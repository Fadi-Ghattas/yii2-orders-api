<?php

namespace api\modules\v1\models;

use yii\base\Model;
use yii\helpers\ArrayHelper;

class OrderItemAddOnForm extends Model
{
    public $id;
    public $quantity;

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                [['id', 'quantity'], 'required'],
                [['id', 'quantity'], 'integer'],
            ]);
    }
}