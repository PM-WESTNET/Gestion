<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\checkout\models\Track */

$this->title = Yii::t('app', 'Create Track');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tracks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="track-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
