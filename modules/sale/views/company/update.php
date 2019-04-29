<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\Company */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Company',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Companies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->company_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="company-update">
    
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
