<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\instructive\models\InstructiveCategory */

$this->title = Yii::t('app','Update Instructive Category: {model}', ['model' => $model->name]);
$this->params['breadcrumbs'][] = ['label' => 'Instructive Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->instructive_category_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="instructive-category-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'roles' => $roles
    ]) ?>

</div>
