<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[ItemChoices]].
 *
 * @see ItemChoices
 */
class ItemChoicesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ItemChoices[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ItemChoices|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
