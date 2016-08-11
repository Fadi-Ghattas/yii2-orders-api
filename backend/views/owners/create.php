<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Owners */

$this->title = 'Create Owners';
$this->params['breadcrumbs'][] = ['label' => 'Owners', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="owners-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
