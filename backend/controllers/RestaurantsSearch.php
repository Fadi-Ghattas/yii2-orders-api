<?php

namespace backend\controllers;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Restaurants;

/**
 * RestaurantsSearch represents the model behind the search form about `common\models\Restaurants`.
 */
class RestaurantsSearch extends Restaurants
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'halal', 'featured', 'disable_ordering', 'delivery_duration', 'status', 'owner_id', 'user_id'], 'integer'],
            [['name', 'time_order_open', 'time_order_close', 'phone_number', 'working_hours', 'image', 'created_at', 'updated_at'], 'safe'],
            [['minimum_order_amount', 'delivery_fee', 'rank', 'longitude', 'latitude'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Restaurants::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'minimum_order_amount' => $this->minimum_order_amount,
            'time_order_open' => $this->time_order_open,
            'time_order_close' => $this->time_order_close,
            'delivery_fee' => $this->delivery_fee,
            'rank' => $this->rank,
            'halal' => $this->halal,
            'featured' => $this->featured,
            'disable_ordering' => $this->disable_ordering,
            'delivery_duration' => $this->delivery_duration,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'owner_id' => $this->owner_id,
            'user_id' => $this->user_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'phone_number', $this->phone_number])
            ->andFilterWhere(['like', 'working_hours', $this->working_hours])
            ->andFilterWhere(['like', 'image', $this->image]);

        return $dataProvider;
    }
}
