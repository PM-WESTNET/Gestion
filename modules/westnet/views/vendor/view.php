<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\Vendor */

$this->title = Yii::t('westnet', 'Vendor') . ' - ' . $model->name . ', ' . $model->lastname;
$this->params['breadcrumbs'][] = ['label' => Yii::t('westnet','Vendors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-vendor-view">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->vendor_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->vendor_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>

    <?php
    echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => Yii::t('app', 'Username'),
                'value' => function($model){
                    return $model->user ? $model->user->username : '';
                }
            ],
            [
                'label' => Yii::t('app', 'Status'),
                'value' => function($model){
                    if($model->user){
                        return $model->user->status ? Yii::t('app', 'Enabled') : Yii::t('app', 'Disabled');
                    }
                    return '';
                },
            ],
            'name',
            'lastname',
            [
                'label' => Yii::t('app', 'External User'),
                'value' => function($model){
                    return $model->externalUser ? $model->externalUser->nombre : '';
                },
            ],
            [
                'label'=>Yii::t('app','Document Type'),
                'value'=> $model->documentType->name,
            ],
            [
                'label'=>Yii::t('app','Document Number'),
                'value'=> $model->document_number,
            ],
            [
                'label'=>Yii::t('app','Phone'),
                'value'=> $model->phone,
            ],            
            [
                'label'=>Yii::t('westnet', 'Commission'),
                'value'=>$model->commission ? $model->commission->name : "",
            ],
            [
                'label'=>Yii::t('app','Address'),
                'value'=> $model->address ? $model->address->fulladdress : "" ,
            ],
            [
                'label'=>Yii::t('app','Provider'),
                'value'=>($model->provider ? $model->provider->name : ""),
            ],
            [
                'label' => Yii::t('app', 'Access to companies'),
                'value' => function($model){
                    $access_companies = '';
                    if($model->user){
                        foreach ($model->user->companies as $company){
                            $access_companies .= $company->name . ' ';
                        }
                        return $access_companies;
                    } else {
                        return '';
                    }
                }
            ]
            
        ],
    ]) ?>


</div>
