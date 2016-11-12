<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Owners]].
 *
 * @see Owners
 */
class OwnersQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Owners[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Owners|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
