<?php

namespace api\modules\v1\models;

use yii\base\Model;
use yii\helpers\ArrayHelper;

class MakeOrderForm extends Model
{
    public $restaurant_id;
    public $address_id;
    public $payment_method_id;
    public $items;
    public $note;
    public $voucher_code;

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                [['restaurant_id', 'address_id', 'payment_method_id', 'items'], 'required'],
                [['restaurant_id', 'address_id', 'payment_method_id'], 'integer'],
                [['voucher_code'], 'string', 'max' => 255],
                [['note' , 'items','voucher_code'], 'safe'],
            ]);
    }
}