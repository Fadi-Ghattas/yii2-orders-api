<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[MenuItemAddon]].
 *
 * @see MenuItemAddon
 */
class MenuItemAddonQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return MenuItemAddon[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return MenuItemAddon|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
