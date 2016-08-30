<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[OrderItemAddon]].
 *
 * @see OrderItemAddon
 */
class OrderItemAddonQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return OrderItemAddon[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return OrderItemAddon|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
