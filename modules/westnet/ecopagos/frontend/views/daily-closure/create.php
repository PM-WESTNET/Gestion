<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\DailyClosure */

$this->title = Yii::t('app', 'Create Daily Closure');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Daily Closures'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="daily-closure-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
