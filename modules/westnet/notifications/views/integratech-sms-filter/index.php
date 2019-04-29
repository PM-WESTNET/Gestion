<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\westnet\notifications\models\search\IntegratechSmsFilterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \app\modules\westnet\notifications\NotificationsModule::t('app','Integratech Sms Filters');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="integratech-sms-filter-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . \app\modules\westnet\notifications\NotificationsModule::t('app','Create Integratech Sms Filter'),
        ['create'], 
        ['class' => 'btn btn-success']) 
        ;?>
    </p>
    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'word:ntext',
            'action',

            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
