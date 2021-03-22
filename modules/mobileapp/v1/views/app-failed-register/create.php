<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\mobileapp\v1\models\AppFailedRegister */

$this->title = Yii::t('app', 'Create App Failed Register');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'App Failed Registers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="app-failed-register-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
