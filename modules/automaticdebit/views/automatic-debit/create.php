<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\automaticdebit\models\AutomaticDebit */

$this->title = Yii::t('app', 'Create Automatic Debit');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Automatic Debits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="automatic-debit-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'banks' => $banks
    ]) ?>

</div>
