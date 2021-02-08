<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\automaticdebit\models\Bank */

$this->title = Yii::t('app', 'Create Bank');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Banks for Automatic Debit'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bank-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
