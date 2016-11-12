<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[PaymentMethodRestaurant]].
 *
 * @see PaymentMethodRestaurant
 */
class PaymentMethodRestaurantQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return PaymentMethodRestaurant[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return PaymentMethodRestaurant|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
