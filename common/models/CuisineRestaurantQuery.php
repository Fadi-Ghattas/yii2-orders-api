<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[CuisineRestaurant]].
 *
 * @see CuisineRestaurant
 */
class CuisineRestaurantQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return CuisineRestaurant[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return CuisineRestaurant|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
