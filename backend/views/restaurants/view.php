<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Restaurants */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Restaurants', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="restaurants-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'minimum_order_amount',
            'time_order_open',
            'time_order_close',
            'delivery_fee',
            'rank',
            'halal',
            'featured',
            'disable_ordering',
            'delivery_duration',
            'phone_number',
            'working_hours',
            'longitude',
            'latitude',
            'image',
            'status',
            'created_at',
            'updated_at',
            'owner_id',
            'user_id',
        ],
    ]) ?>

</div>
