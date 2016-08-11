<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[AreaRestaurant]].
 *
 * @see AreaRestaurant
 */
class AreaRestaurantQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return AreaRestaurant[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return AreaRestaurant|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
