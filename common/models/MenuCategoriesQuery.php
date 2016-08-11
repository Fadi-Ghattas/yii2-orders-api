<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[MenuCategories]].
 *
 * @see MenuCategories
 */
class MenuCategoriesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return MenuCategories[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return MenuCategories|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
