<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('accounting', 'Conciliations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="conciliation-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', ['modelClass'=>Yii::t('accounting','Conciliation')]), ['create'], ['class' => 'btn btn-success']) ?>
        </p>        
    </div>

    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' =>  Yii::t('accounting', 'Money Box Account'),
                'value' => function ($model) {
                    return $model->moneyBoxAccount->moneyBox->name . " - " . $model->moneyBoxAccount->number;
                }
            ],
            'name',
            'date',
            'date_from',
            'date_to',
            [
                'label' => Yii::t('app', 'Status'),
                'value' => function ($model) {
                    return Yii::t('accounting', ucfirst($model->status));
                }
            ],

            [
                'header' => Yii::t('accounting','Conciliation'),
                'format' => 'html',
                'value' => function($model){
                    if ($model->status=='draft') {
                        return Html::a('<span class="glyphicon glyphicon-indent-right"></span> '.Yii::t('accounting','Make'),
                            ['conciliation/conciliate','id'=>$model->conciliation_id], ['class'=>'btn btn-warning']);
                    } else {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span> '.Yii::t('yii','View'),
                            ['conciliation/conciliate','id'=>$model->conciliation_id, 'readOnly'=>true], ['class'=>'btn btn-default']);
                    }
                }
            ],
            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
