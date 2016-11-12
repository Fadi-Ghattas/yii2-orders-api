<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[ClientsVouchers]].
 *
 * @see ClientsVouchers
 */
class ClientsVouchersQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ClientsVouchers[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ClientsVouchers|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
