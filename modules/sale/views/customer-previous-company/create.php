<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\CustomerPreviousCompany */

$this->title = 'Create Customer Previous Company';
$this->params['breadcrumbs'][] = ['label' => 'Customer Previous Companies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-previous-company-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
