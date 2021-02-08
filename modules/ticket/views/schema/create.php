<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\ticket\models\Schema */

$this->title = Yii::t('app', 'Create Schema');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Schemas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="schema-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
