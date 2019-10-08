<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\Company */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Companies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-view">

    <?php 
    //Mensaje
    if(!Yii::$app->params['companies']['enabled'])
        echo \yii\bootstrap\Alert::widget([
        'options' => [
            'class' => 'alert-warning',
        ],
          'body' => Yii::t('app', 'Only visible to superadmin.'),
    ]); ?>
    <div class="row text-center">
        <div class="col-xs-12">
            <?php 
            //Falta config?
            if(!$model->defaultPointOfSale){
                echo yii\bootstrap\Alert::widget([
                    'options' => [
                        'class' => 'btn btn-danger',
                    ],
                    'body' => Yii::t('app', 'Warning! This company has not got a default point of sale.'),   
                ]);
            } ?>            
        </div>
    </div>
    
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        
        <p>
            <?= Html::a(
                "<span class='glyphicon glyphicon-plus'></span> " .
                Yii::t('app', 'Create {modelClass}', ['modelClass' => Yii::t('app', 'Point of Sale')]), 
                ['point-of-sale/create', 'company' => $model->company_id], 
                ['class' => 'btn btn-success']) 
            ?>
            <?= Html::a(
                "<span class='glyphicon glyphicon-pencil'></span> " .
                Yii::t('app', 'Update'), 
                ['update', 'id' => $model->company_id], 
                ['class' => 'btn btn-primary']) 
            ?>
            <?php if($model->deletable) echo Html::a("<span class='glyphicon glyphicon-remove'></span> " .Yii::t('app', 'Delete'), ['delete', 'id' => $model->company_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>
    

   <div class="table-responsive">
        <?= DetailView::widget([
            'model' => $model,
            
            'attributes' => [
                'company_id',
                'name',
                'fantasy_name',
                [
                    'attribute' => 'partnerDistributionModel.name',
                    'label' => Yii::t('partner', 'Partner Distribution Model')
                ],
                [
                    'attribute' => 'status',
                    'value' => Yii::t('app', ucfirst($model->status))
                ],
                'default:boolean',
                'tax_identification',
                'address',
                'phone',
                'technical_service_phone',
                'email:email',
                [
                    'attribute' => 'parent_id',
                    'value' => $model->name
                ],
                'certificate',
                'key',
                [
                    'label' => Yii::t('app', 'logo'),
                    'value' => ($model->logo ? Html::img(Yii::$app->request->baseUrl .'/'. $model->getLogoWebPath(), ['class'=>'file-preview-image', 'alt'=>'', 'title'=>'']) : '' ),
                    'format' => 'raw'
                ],

                'create_timestamp:datetime',
                'iibb',
                'start',
                'code',
                'web',
                'portal_web',
                [
                    'attribute' => 'pagomiscuentas_code',
                    'value' => function ($model){
                        return str_pad($model->pagomiscuentas_code, 4, '0', STR_PAD_LEFT);
                    }
                ],
            ],
        ]) ?>
   </div>
    
    <h3 class="font-l margin-top-double margin-bottom-half"><?= Yii::t('app', 'Points of Sale') ?></h3>
    <?= GridView::widget([
        'dataProvider' => $salePoints,
        'options' => ['class' => 'table-responsive'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            'number',
            [
                'attribute'=>'status',
                'filter'=>[
                    'enabled'=>Yii::t('app','Enabled'),
                    'disabled'=>Yii::t('app','Disabled'),
                ],
                'value'=>function($model){return Yii::t('app',  ucfirst($model->status)); }
            ],
            'description',
            [
                'class' => 'app\components\companies\CompanyColumn'
            ],
            'electronic_billing:boolean',
            [
                'class' => 'app\components\grid\ActionColumn',
                'controller' => 'point-of-sale',
            ],
        ],
    ]); ?>

</div>
