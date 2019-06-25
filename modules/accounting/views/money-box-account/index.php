<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('accounting', 'Money Box Accounts');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="money-box-account-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', ['modelClass'=>Yii::t('accounting','Money Box Account')]), ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    </div>
    

    <?php

    $columns =[
        ['class' => 'yii\grid\SerialColumn'],
        [
            'header'=> Yii::t('accounting', 'Money Box'),
            'value'=>function($model){ if(!empty($model->moneyBox)) return $model->moneyBox->name; }
        ],
        'number',
        [
            'label'     => Yii::t('app', 'Currency'),
            'attribute' => 'currency.name',
        ],
        'enable:boolean',
    ];

    //Columna de empresa, solo si se encuentra activa la func. de empresas
    if(Yii::$app->params['companies']['enabled']){
        $columns[] = ['class' => 'app\components\companies\CompanyColumn'];
    }

    $columns[] = [
        'header' => Yii::t('accounting','Movements'),
        'format' => 'html',
        'value' => function($model){
            return Html::a('<span class="glyphicon glyphicon-eye-open"></span> '.Yii::t('yii','View'),
                ['money-box-account/movements','id'=>$model->money_box_account_id], ['class'=>'btn btn-default']);
        }
    ];
    $columns[] = ['class' => 'app\components\grid\ActionColumn',];

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columns
    ]); ?>

</div>
