<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\AccessPoint */

$this->title = Yii::t('app', 'Create Access Point');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Access Points'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="access-point-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'nodes' => $nodes,
    ]) ?>

</div>
