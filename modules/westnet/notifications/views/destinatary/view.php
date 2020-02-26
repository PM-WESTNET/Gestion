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

            <div class="col-lg-3 text-center">

                <span class="font-bold"><?= NotificationsModule::t('app', 'Nodes'); ?></span>

            </div>

            <div class="col-lg-9">

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

            <div class="col-lg-3 text-center">

                <span class="font-bold"><?= NotificationsModule::t('app', 'Company'); ?></span>

            </div>

            <div class="col-lg-9">

                <?php if (!empty($model->companiesObject)) : ?>

                    <?php foreach ($model->companiesObject as $company) : ?>

                        <label class="badge">
                            <?= $company->name; ?>
                        </label>

                    <?php endforeach; ?>

                <?php else: ?>

                    <p><?= NotificationsModule::t('app', 'No nodes were selected'); ?></p>

                <?php endif; ?>
            </div>

        </div>
        <!-- end Company -->

               
         <!-- Customer Status  -->
        <div class="row valign-wrapper padding-top-quarter padding-bottom-quarter">

            <div class="col-lg-3 text-center">

                <span class="font-bold"><?= NotificationsModule::t('app', 'Customer status'); ?></span>

            </div>

            <div class="col-lg-9">
                <?php if(!empty($model->customerStatuses)):?>
                    <?php foreach ($model->customerStatuses as $customer_status):?>
                        <label class="badge">
                            <?=  NotificationsModule::t('app', $customer_status->customer_status) ?>
                        </label>                
                    <?php endforeach;?>
                <?php else:?>
                    <label class="badge">
                        <?php 
                        echo NotificationsModule::t('app', 'Without criteria'); ?>
                    </label>
                <?php endif;?>
            </div>

        </div>
        <!-- end Customer -->
        
        <!-- Customer Category -->
        <div class="row valign-wrapper padding-top-quarter padding-bottom-quarter">

            <div class="col-lg-3 text-center">

                <span class="font-bold"><?= NotificationsModule::t('app', 'Customer Category'); ?></span>

            </div>

            <div class="col-lg-9">
                <?php if(!empty($model->customerCategories)):?>
                    <?php foreach ($model->customerCategories as $customer_category):?>
                        <label class="badge">
                            <?=  NotificationsModule::t('app', $customer_category->name) ?>
                        </label>                
                    <?php endforeach;?>
                <?php else:?>
                    <label class="badge">
                        <?php 
                        echo NotificationsModule::t('app', 'Without criteria'); ?>
                    </label>
                <?php endif;?>
            </div>

        </div>
        <!-- end Customer -->
        
        <!-- Customer Class -->
        <div class="row valign-wrapper padding-top-quarter padding-bottom-quarter">

            <div class="col-lg-3 text-center">

                <span class="font-bold"><?= NotificationsModule::t('app', 'Customer class'); ?></span>

            </div>

            <div class="col-lg-9">
                <?php if(!empty($model->customerClasses)):?>
                    <?php foreach ($model->customerClasses as $customer_class):?>
                        <label class="badge">
                            <?=  NotificationsModule::t('app', $customer_class->name) ?>
                        </label>                
                    <?php endforeach;?>
                <?php else:?>
                    <label class="badge">
                        <?php 
                        echo NotificationsModule::t('app', 'Without criteria'); ?>
                    </label>
                <?php endif;?>
            </div>

        </div>
        <!-- end Customer -->


        
        <!-- Contract Status -->
        <div class="row valign-wrapper padding-top-quarter padding-bottom-quarter">

            <div class="col-lg-3 text-center">

                <span class="font-bold"><?= NotificationsModule::t('app', 'Contract status'); ?></span>

            </div>

            <div class="col-lg-9">
                <?php if(!empty($model->contractStatuses)):?>
                    <?php foreach ($model->contractStatuses as $contract_status):?>
                        <label class="badge">
                            <?=  NotificationsModule::t('app', $contract_status->contract_status) ?>
                        </label>                
                    <?php endforeach;?>
                <?php else:?>
                    <label class="badge">
                        <?php 
                        echo NotificationsModule::t('app', 'Without criteria'); ?>
                    </label>
                <?php endif;?>
            </div>

        </div>
        <!-- end Customer -->
        
        <!-- Plans -->
        <div class="row valign-wrapper padding-top-quarter padding-bottom-quarter">

            <div class="col-lg-3 text-center">

                <span class="font-bold"><?= NotificationsModule::t('app', 'Plans'); ?></span>

            </div>

            <div class="col-lg-9">
                <?php if(!empty($model->plans)):?>
                    <?php foreach ($model->plans as $plan):?>
                        <label class="badge">
                            <?=  NotificationsModule::t('app', $plan->name) ?>
                        </label>                
                    <?php endforeach;?>
                <?php else:?>
                    <label class="badge">
                        <?php 
                        echo NotificationsModule::t('app', 'Without criteria'); ?>
                    </label>
                <?php endif;?>
            </div>

        </div>
        <!-- end Customer -->

        <!-- Contract age info -->
        <div class="row valign-wrapper padding-top-quarter padding-bottom-quarter">

            <div class="col-lg-3 text-center">

                <span class="font-bold"><?= NotificationsModule::t('app', 'Contract Age'); ?></span>

            </div>

            <div class="col-lg-9">

                <label class="badge">
                    <?= NotificationsModule::t('app', 'minimun'); ?>: <?= (!empty($model->contract_min_age)) ? $model->contract_min_age . ' ' . NotificationsModule::t('app', 'months') : NotificationsModule::t('app', 'Without criteria'); ?>
                </label>

                <label class="badge">
                    <?= NotificationsModule::t('app', 'maximun'); ?>: <?= (!empty($model->contract_max_age)) ? $model->contract_max_age . ' ' . NotificationsModule::t('app', 'months') : NotificationsModule::t('app', 'Without criteria'); ?>
                </label>

            </div>

        </div>
        <!-- end Contract age info -->


        <!-- Billing info -->
        <div class="row valign-wrapper padding-top-quarter padding-bottom-quarter">

            <div class="col-lg-3 text-center">

                <span class="font-bold"><?= NotificationsModule::t('app', 'Billing'); ?></span>

            </div>

            <div class="col-lg-9">

                <label class="badge">
                    <?= NotificationsModule::t('app', 'Overdue bills from'); ?>: <?= (!empty($model->overdue_bills_from)) ? $model->overdue_bills_from : NotificationsModule::t('app', 'Without criteria'); ?>
                </label>

                <label class="badge">
                    <?= NotificationsModule::t('app', 'Overdue bills to'); ?>: <?= (!empty($model->overdue_bills_to)) ? $model->overdue_bills_to : NotificationsModule::t('app', 'Without criteria'); ?>
                </label>

            </div>

        </div>
        <!-- end Billing info -->
        
        <!-- Debth info -->
        <div class="row valign-wrapper padding-top-quarter padding-bottom-quarter">

            <div class="col-lg-3 text-center">

                <span class="font-bold"><?= NotificationsModule::t('app', 'Debt'); ?></span>

            </div>

            <div class="col-lg-9">

                <label class="badge">
                    <?= NotificationsModule::t('app', 'From'); ?>: <?= (!empty($model->debt_from)) ? Yii::$app->formatter->asCurrency($model->debt_from) : NotificationsModule::t('app', 'Without criteria'); ?>
                </label>

                <label class="badge">
                    <?= NotificationsModule::t('app', 'To'); ?>: <?= (!empty($model->debt_to)) ? Yii::$app->formatter->asCurrency($model->debt_to) : NotificationsModule::t('app', 'Without criteria'); ?>
                </label>

            </div>

        </div>
        <!-- end Debth info -->

        <!-- Debth info -->
        <div class="row valign-wrapper padding-top-quarter padding-bottom-quarter">

            <div class="col-lg-3 text-center">

                <span class="font-bold"><?= NotificationsModule::t('app', 'App'); ?></span>

            </div>

            <div class="col-lg-9">
                <?php if (empty($model->has_app)):?>
                    <label class="badge">
                        <?php echo NotificationsModule::t('app', 'Without criteria')?>
                    </label>
                <?php endif;?>

                <?php if (!empty($model->has_app) && $model->has_app === 'installed'):?>
                    <label class="badge">
                        <?php echo NotificationsModule::t('app', 'Installed')?>
                    </label>
                <?php endif;?>

                <?php if (!empty($model->has_app) && $model->has_app === 'not_installed'):?>
                    <label class="badge">
                        <?php echo NotificationsModule::t('app', 'Not Installed')?>
                    </label>
                <?php endif;?>

            </div>

        </div>
        <!-- end Debth info -->

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
                'value' => function($model){ return $model['name']; }
            ],
            [
                'label' => Yii::t('app', 'Email'),
                'attribute'=>'email',
            ],
            [
                'label' => Yii::t('app', 'Email Status'),
                'attribute'=>'email_status',
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
            <?php // Html::a(Yii::t('app', 'Send'), ['send', 'notification_id' => $model->notification->notification_id], ['class' => 'btn btn-primary']) ?>
        </div>

    </div>

</div>
<!-- end Customers filter -->    