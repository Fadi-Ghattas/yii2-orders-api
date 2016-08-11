<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[FavoriteRestaurants]].
 *
 * @see FavoriteRestaurants
 */
class FavoriteRestaurantsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return FavoriteRestaurants[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return FavoriteRestaurants|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
