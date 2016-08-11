<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Vouchers]].
 *
 * @see Vouchers
 */
class VouchersQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Vouchers[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Vouchers|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
