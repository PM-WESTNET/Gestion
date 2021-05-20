<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\firstdata\models\FirstdataAutomaticDebit */

$this->title = Yii::t('app', 'Create Firstdata Automatic Debit');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Firstdata Automatic Debits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="firstdata-automatic-debit-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'roles_for_adherence' => $roles_for_adherence,
    ]) ?>

</div>
