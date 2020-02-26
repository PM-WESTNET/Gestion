<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\employee\models\EmployeeCategory */

$this->title = Yii::t('app', 'Create Employee Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Employee Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
