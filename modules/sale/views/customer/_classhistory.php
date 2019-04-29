<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $model app\models\Customer */

$this->title = Yii::t('app', 'Class History') . ': ' . Html::encode($model->name);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->customer_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="class-customer">

    <h1><?= $this->title ?></h1>

    
   
<?php
    echo GridView::widget([
        'dataProvider' =>  \app\modules\sale\models\search\CustomerSearch::getdataProviderClasses($model->customer_id),
        'columns' => [
            [
                'label' => Yii::t('app', 'Category'),
                'attribute' => 'customerClass.name'
            ],
            [
                'label' => Yii::t('app', 'Updated'),
                'attribute' => 'date_updated',
                'format' => 'datetime'
            ]
        ]
    ]);

?>    

</div>