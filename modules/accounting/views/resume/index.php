<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('accounting', 'Resumes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="resume-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', [
        'modelClass' => Yii::t('accounting', 'Resume'),
    ]), 
            ['create'], 
            ['class' => 'btn btn-success']) 
            ;?>
        </p>
    </div>


    <?php

    $item = '<span class="glyphicon glyphicon-chevron-down"></span> ' . Yii::t('app', 'Filters');

    echo \yii\bootstrap\Collapse::widget([
        'items' => [
            [
                'label' => $item,
                'content' => $this->render('_search', ['model' => $searchModel, 'embed' => false]),
                'encode' => false,
            ],
        ]
    ]);

    $columns[] = ['class' => 'yii\grid\SerialColumn'];

    if(Yii::$app->params['companies']['enabled']){
        $columns[] = ['class' => 'app\components\companies\CompanyColumn'];
    }

    $columns  = array_merge($columns,[
        'name',
        [
            'label' =>  Yii::t('accounting', 'Money Box Account'),
            'value' => function ($model) {
                return $model->moneyBoxAccount->moneyBox->name . " - " . $model->moneyBoxAccount->number ;
            }
        ],
        'date:date',
        'date_from:date',
        'date_to:date',
        [
            'label' => Yii::t('app', 'Status'),
            'value' => function($model) {
                return Yii::t('accounting', ucfirst($model->status));
            }
        ],
        [
            'header' => Yii::t('app','Detail'),
            'format' => 'html',
            'value' => function($model){ return Html::a('<span class="glyphicon glyphicon-eye-open"></span> '.Yii::t('yii','View'), ['resume/details','id'=>$model->resume_id], ['class'=>'btn btn-default']); }
        ],
        [
            'class' => 'app\components\grid\ActionColumn',
            'template'=>'{view} {update} {delete}',
            'buttons'=>[
                'update' => function ($url, $model, $key) {
                    return $model->status === 'draft' ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, ['class' => 'btn btn-primary']) : '';
                },
                'delete' => function ($url, $model, $key) {
                    if($model->getDeletable()){
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', yii\helpers\Url::toRoute(['resume/delete', 'id'=>$key]), [
                            'title' => Yii::t('yii', 'Delete'),
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'data-method' => 'post',
                            'data-pjax' => '1',
                            'class' => 'btn btn-danger',
                        ]);
                    }
                }

            ]
        ]
    ]);


    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $columns
    ]); ?>

</div>
