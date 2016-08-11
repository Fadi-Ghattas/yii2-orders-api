<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Addons]].
 *
 * @see Addons
 */
class AddonsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Addons[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Addons|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
