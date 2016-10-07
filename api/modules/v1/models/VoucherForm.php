<?php

namespace api\modules\v1\models;

use yii\base\Model;
use yii\helpers\ArrayHelper;

class VoucherForm extends Model
{
    public $restaurant_id;
    public $order_total_amount;
    public $voucher_code;

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                [['restaurant_id', 'order_total_amount', 'voucher_code'], 'required'],
                [['voucher_code'], 'number'],
            ]);
    }
}