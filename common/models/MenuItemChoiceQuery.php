<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[MenuItemChoice]].
 *
 * @see MenuItemChoice
 */
class MenuItemChoiceQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return MenuItemChoice[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return MenuItemChoice|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
