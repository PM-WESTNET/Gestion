<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\grid\GridView;
use app\modules\westnet\notifications\NotificationsModule;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\notifications\models\Destinatary */
/* @var $form yii\widgets\ActiveForm */

$this->title = NotificationsModule::t('app', 'Selection of contracts');
$this->params['breadcrumbs'][] = ['label' => NotificationsModule::t('app', 'Notifications'), 'url' => ['notification/index']];
$this->params['breadcrumbs'][] = ['label' => $model->notification->name, 'url' => ['notification/view', 'id' => $model->notification->notification_id]];
$this->params['breadcrumbs'][] = ['label' => NotificationsModule::t('app', 'Destinataries'), 'url' => ['update', 'id' => $model->destinatary_id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="destinatary-create padding-left-full padding-right-full">

    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Search information -->
    <div class="container">

        <h3><?= NotificationsModule::t('app', 'Query information'); ?></h3>

        <!-- Node -->
        <div class="row valign-wrapper padding-top-quarter padding-bottom-quarter">

            <div class="col-lg-2 text-center">

                <span class="font-bold"><?= NotificationsModule::t('app', 'Nodes'); ?></span>

            </div>

            <div class="col-lg-10">

                <?php if (!empty($model->nodes)) : ?>

                    <?php foreach ($model->nodes as $node) : ?>

                        <label class="badge">
                            <?= $node->name; ?>
                        </label>

                    <?php endforeach; ?>

                <?php else: ?>

                    <p><?= NotificationsModule::t('app', 'No nodes were selected'); ?></p>

                <?php endif; ?>

            </div>

        </div>
        <!-- end Node -->

        <!-- Company -->
        <div class="row valign-wrapper padding-top-quarter padding-bottom-quarter">

            <div class="col-lg-2 text-center">

                <span class="font-bold"><?= NotificationsModule::t('app', 'Company'); ?></span>

            </div>

            <div class="col-lg-10">

                <?php if (!empty($model->company)) : ?>

                    <label class="badge">
                        <?= $model->company->name; ?>
                    </label>

                <?php else: ?>

                    <p><?= NotificationsModule::t('app', 'No company were selected'); ?></p>

                <?php endif; ?>

            </div>

        </div>
        <!-- end Company -->

        <!-- Customer -->
        <div class="row valign-wrapper padding-top-quarter padding-bottom-quarter">

            <div class="col-lg-2 text-center">

                <span class="font-bold"><?= NotificationsModule::t('app', 'Customer'); ?></span>

            </div>

            <div class="col-lg-10">

                <label class="badge">
                    <?= NotificationsModule::t('app', 'Name'); ?>: <?= (!empty($model->name)) ? $model->name : NotificationsModule::t('app', 'Without criteria'); ?>
                </label>

                <label class="badge">
                    <?= NotificationsModule::t('app', 'Ip'); ?>: <?= (!empty($model->ip)) ? $model->ip : NotificationsModule::t('app', 'Without criteria'); ?>
                </label>
                
                <label class="badge">
                    <?= NotificationsModule::t('app', 'Status'); ?>: <?= (!empty($model->customer_status)) ? NotificationsModule::t('app', $model->customer_status) : NotificationsModule::t('app', 'Without criteria'); ?>
                </label>

            </div>

        </div>
        <!-- end Customer -->

        <!-- Billing info -->
        <div class="row valign-wrapper padding-top-quarter padding-bottom-quarter">

            <div class="col-lg-2 text-center">

                <span class="font-bold"><?= NotificationsModule::t('app', 'Billing'); ?></span>

            </div>

            <div class="col-lg-10">

                <label class="badge">
                    <?= NotificationsModule::t('app', 'Overdue bills from'); ?>: <?= (!empty($model->overdue_bills_from)) ? $model->overdue_bills_from : NotificationsModule::t('app', 'Without criteria'); ?>
                </label>

                <label class="badge">
                    <?= NotificationsModule::t('app', 'Overdue bills to'); ?>: <?= (!empty($model->overdue_bills_to)) ? $model->overdue_bills_to : NotificationsModule::t('app', 'Without criteria'); ?>
                </label>

            </div>

        </div>
        <!-- end Billing info -->

    </div>
    <!-- end Search information -->

    <!-- Customers filter -->
    <div class="customer-list container-fluid margin-bottom-full">
        
        <?php
        $columns = [
//            ['class' => '\kartik\grid\CheckboxColumn'],
            [
                'label' => Yii::t('app', 'Customer'),
                'attribute'=>'name',
            ],
            [
                'label' => Yii::t('app', 'Email'),
                'attribute'=>'email',
            ],
            [
                'label' => Yii::t('app', 'Phone'),
                'attribute'=>'phone',
            ],
        ];

        echo "<h3>Cantidad actual: $dataProvider->totalCount</h3>";
        
        $grid = GridView::begin([
            'dataProvider' => $dataProvider,
            'id' => 'grid',
            'filterModel' => false,
            'options' => ['class' => 'table-responsive'],
            'columns' => $columns,
        ]);
        ?>

        <?php $grid->end(); ?>

        <div class="form-group">
            <?= Html::a(Yii::t('app', 'Send'), ['send', 'notification_id' => $model->notification->notification_id], ['class' => 'btn btn-primary']) ?>
        </div>

    </div>

</div>
<!-- end Customers filter -->    