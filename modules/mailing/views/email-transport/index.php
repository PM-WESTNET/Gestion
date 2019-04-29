<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \app\modules\mailing\MailingModule::t('Email Transports');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-transport-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', [
    'modelClass' => \app\modules\mailing\MailingModule::t('Email Transport'),
]), 
        ['create'], 
        ['class' => 'btn btn-success']) 
        ;?>
    </p>
    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'from_email:email',
            'transport',
            'host',
            'port',
            'username',
            'relation_class',
            ['class' => 'app\components\grid\ActionColumn'],
            [
                'header' => \app\modules\mailing\MailingModule::t('Test'),
                'format' => 'raw',
                'value' => function($model){
                    return '<button class="btn btn-default test-transport" data-id="'. $model->email_transport_id.'" data-toggle="modal" data-target="#modalTest"><span class="glyphicon glyphicon-envelope"></span></button>';
                }
            ],
        ],
    ]); ?>
</div>
<?= $this->render('_test'); ?>
