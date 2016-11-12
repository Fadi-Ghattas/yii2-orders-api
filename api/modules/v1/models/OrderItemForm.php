<?php

namespace api\modules\v1\models;

use yii\base\Model;
use yii\helpers\ArrayHelper;

class OrderItemForm extends Model
{
    public $id;
    public $quantity;
    public $note;
    public $add_on;
    public $item_choices;

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                [['id', 'quantity'], 'required'],
                [['id', 'quantity'], 'integer'],
                [['note', 'add_on', 'item_choices'], 'safe'],
            ]);
    }
}