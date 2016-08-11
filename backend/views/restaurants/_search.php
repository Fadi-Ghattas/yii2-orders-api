<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\controllers\RestaurantsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="restaurants-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'minimum_order_amount') ?>

    <?= $form->field($model, 'time_order_open') ?>

    <?= $form->field($model, 'time_order_close') ?>

    <?php // echo $form->field($model, 'delivery_fee') ?>

    <?php // echo $form->field($model, 'rank') ?>

    <?php // echo $form->field($model, 'halal') ?>

    <?php // echo $form->field($model, 'featured') ?>

    <?php // echo $form->field($model, 'disable_ordering') ?>

    <?php // echo $form->field($model, 'delivery_duration') ?>

    <?php // echo $form->field($model, 'phone_number') ?>

    <?php // echo $form->field($model, 'working_hours') ?>

    <?php // echo $form->field($model, 'longitude') ?>

    <?php // echo $form->field($model, 'latitude') ?>

    <?php // echo $form->field($model, 'image') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'owner_id') ?>

    <?php // echo $form->field($model, 'user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
