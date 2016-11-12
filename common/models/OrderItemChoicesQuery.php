<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[OrderItemChoices]].
 *
 * @see OrderItemChoices
 */
class OrderItemChoicesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return OrderItemChoices[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return OrderItemChoices|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
