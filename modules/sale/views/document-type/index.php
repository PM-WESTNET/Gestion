<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\sale\models\search\DocumentTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Document Types');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-type-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', ['modelClass'=>Yii::t('app','Document Type')]), ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    </div>
    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'table-responsive'],                
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'document_type_id',
            'name',
            'code',
            'regex',

            [
                'class' => 'app\components\grid\ActionColumn', 
            ],
        ],
    ]); ?>

</div>
