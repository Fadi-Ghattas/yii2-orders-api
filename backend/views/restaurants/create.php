<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Restaurants */

$this->title = 'Create Restaurants';
$this->params['breadcrumbs'][] = ['label' => 'Restaurants', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="restaurants-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
