<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[MenuCategoryItem]].
 *
 * @see MenuCategoryItem
 */
class MenuCategoryItemQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return MenuCategoryItem[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return MenuCategoryItem|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
