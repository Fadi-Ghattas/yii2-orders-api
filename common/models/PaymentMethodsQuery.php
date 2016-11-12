<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[PaymentMethods]].
 *
 * @see PaymentMethods
 */
class PaymentMethodsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return PaymentMethods[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return PaymentMethods|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
