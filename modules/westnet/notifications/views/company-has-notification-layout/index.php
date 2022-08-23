<?php

use app\components\widgets\agenda\notification\Notification;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\modules\westnet\notifications\NotificationsModule;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\westnet\notifications\models\search\CompanyHasNotificationLayoutSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Company Has Notification Layouts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-has-notification-layout-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Company Has Notification Layout', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            // 'id',
            [
                'attribute' => 'company_id',
                'label' => NotificationsModule::t('app', 'Company'),
                'value' => function ($model) {
                    // if company is empty and throws an error, this models migration could have run with another name due to the dbhelper dynamic table get
                    return $model->company->name;
                },
                // 'format' => 'raw'
            ],
            [
                'attribute' => 'layout_path',
                'label' => NotificationsModule::t('app', 'Layouts Path'),
                'value' => function($model){
                    return $model->layouts_base_path.'/<span class="label label-primary">'.$model->layout_path.'</span>'.'.php';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'is_enabled',
                'label' => NotificationsModule::t('app', 'Is Enabled?'),
                'value' => function($model){
                    $value = ($model->is_enabled) ? NotificationsModule::t('app', 'Yes') : NotificationsModule::t('app', 'No');
                    $status = ($model->is_enabled) ? 'success' : 'danger';
                    return '<span class="label label-'.$status.'">'.$value.'</span>';
                },
                'format' => 'raw'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
