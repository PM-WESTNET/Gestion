<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\ticket\models\Schema */

$this->title = Yii::t('app', 'Update Schema: ') . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Schemas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->schema_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="schema-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
