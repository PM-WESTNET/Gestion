<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\PointOfSale */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => Yii::t('app','Point of Sale'),
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Points of Sale'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->point_of_sale_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="point-of-sale-update">

    <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
            <?php 
            //Mensaje
            if(!Yii::$app->params['companies']['enabled'])
                echo \yii\bootstrap\Alert::widget([
                'options' => [
                    'class' => 'alert-warning',
                ],
                  'body' => Yii::t('app', 'Only visible to superadmin.'),
             ]); ?>
            
            <h1><?= Html::encode($this->title) ?></h1>

            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
