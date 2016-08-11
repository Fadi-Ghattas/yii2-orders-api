<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Restaurants */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="restaurants-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'minimum_order_amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'time_order_open')->textInput() ?>

    <?= $form->field($model, 'time_order_close')->textInput() ?>

    <?= $form->field($model, 'delivery_fee')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'rank')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'halal')->textInput() ?>

    <?= $form->field($model, 'featured')->textInput() ?>

    <?= $form->field($model, 'disable_ordering')->textInput() ?>

    <?= $form->field($model, 'delivery_duration')->textInput() ?>

    <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'working_hours')->textInput() ?>

    <?= $form->field($model, 'longitude')->textInput() ?>

    <?= $form->field($model, 'latitude')->textInput() ?>

    <?= $form->field($model, 'image')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>
    
    <?= $form->field($model, 'mangerName')->textInput() ?>

    <?= $form->field($model, 'mangerEmail')->textInput() ?>

    <?= $form->field($model, 'mangerPassWord')->passwordInput() ?>

    <?= $form->field($model, 'ownerName')->textInput() ?>

    <?= $form->field($model, 'ownerContactNumber')->textInput() ?>

    <?= $form->field($model, 'ownerEmail')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
