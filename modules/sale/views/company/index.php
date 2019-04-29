<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\sale\models\search\CompanySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Companies');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-index">
    
    <?php 
    //Mensaje
    if(!Yii::$app->params['companies']['enabled'])
        echo \yii\bootstrap\Alert::widget([
        'options' => [
            'class' => 'alert-warning',
        ],
          'body' => Yii::t('app', 'Only visible to superadmin.'),
    ]); ?>

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', ['modelClass' => Yii::t('app', 'Company')]), 
            ['create'], 
            ['class' => 'btn btn-success']) 
            ;?>
        </p>
    </div>
    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'table-responsive'],                
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'company_id',
            'name',
            'fantasy_name',
            [
                'attribute' => 'status',
                'value' => function($model){ return Yii::t('app', ucfirst($model->status)); }
            ],
            [
                'attribute' => 'default',
                'filter' => [0 => Yii::t('yii', 'No'), 1 => Yii::t('yii', 'Yes')],
                'format' => ['boolean']
            ],
            'tax_identification',
            'address',
            // 'phone',
            // 'email:email',
            [
                'header'=> Yii::t('app','Parent'),
                'value'=>function($model){ if(!empty($model->parent)) return $model->parent->name; }
            ],       
            [
                'header' => Yii::t('app','Bill Types'),
                'value' => function($model){ 
                    $types = '';
                    foreach($model->billTypes as $i => $type){
                        $label = ($i == 0) ? $type->name : ", $type->name";
                        $types .= Html::a($label, ['bill-type/view', 'id' => $type->bill_type_id]);
                    }
                    return $types;
                },
                'format' => ['html']
            ],

            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
