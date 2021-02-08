<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\instructive\models\Instructive */

$this->title = Yii::t('app','Update {modelClass}', ['modelClass' => Yii::t('app','Instructive')]) . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Instructives'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->instructive_id]];
$this->params['breadcrumbs'][] = Yii::t('app','Update');
?>
<div class="instructive-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'instructiveCategories' => $instructiveCategories,
    ]) ?>

</div>
