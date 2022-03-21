<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\modules\ticket\TicketModule;
use app\modules\ticket\components\ColorHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = TicketModule::t('app', 'Active tickets');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ticket-index container">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= TicketModule::t('app', 'List of customers with active tickets'); ?>
    </p>

    <?php if (!empty($customersWithTickets)) : ?>

        <?php foreach ($customersWithTickets as $customerInfo) : ?>

            <?php
            $customer = app\modules\sale\models\Customer::findOne($customerInfo['customer_id']);
            if($customer):
            $ticketCount = $customerInfo['count'];
            $tickets = app\modules\ticket\models\Ticket::find()->where([
                        'customer_id' => $customer->customer_id,
                    ])->orderBy([
                        'start_datetime' => SORT_ASC
                    ])->all();
            ?>
            <!-- Customer panel -->
            <div class="panel panel-default">

                <div class="panel-heading">
                    <h3 class="panel-title">
                        <strong><?= $ticketCount; ?> <?= TicketModule::t('app', 'Open ticket(s)'); ?></strong> 
                        | <?= $customer->name; ?>
                    </h3>
                </div>

                <div class="panel-body">

                    <div class="panel-group no-margin-bottom" id="accordion-<?= $customer->customer_id; ?>" role="tablist" aria-multiselectable="true">

                        <?php foreach ($tickets as $key => $ticket) : ?>

                            <?php
                            $bgColor = new ColorHelper($ticket->color->color);
                            $bgColorLighten = $bgColor->lighten("15%");
                            ?>

                            <!-- Customer ticket -->
                            <div class="panel panel-default">

                                <a role="button" data-toggle="collapse" data-parent="#accordion-<?= $customer->customer_id; ?>" href="#collapse-ticket-<?= $ticket->ticket_id; ?>" aria-expanded="true" aria-controls="collapseOne">
                                    <div class="panel-heading" role="tab" id="headingOne" style="background-color: #<?= $bgColorLighten; ?>; color: #fff;">
                                        <h4 class="panel-title">

                                            <?= Yii::$app->formatter->asDate($ticket->start_datetime); ?> |                                            
                                            <span class="font-bold"><?= $ticket->title; ?></span>
                                            <span class="label label-default pull-right" style="background-color: <?= $ticket->color->color; ?>;"><?= $ticket->number; ?> (<?= $ticket->color->name; ?>)</span>

                                        </h4>
                                    </div>
                                </a>

                                <div id="collapse-ticket-<?= $ticket->ticket_id; ?>" class="panel-collapse collapse">

                                    <div class="panel-body" style="background-color: #<?= $bgColorLighten; ?>;">
                                        <?=
                                        $this->render("_ticket_info", [
                                            'model' => $ticket
                                        ]);
                                        ?>
                                    </div>

                                    <div class="panel-footer">
                                        <?= Html::a('<span class="glyphicon glyphicon-eye-open"></span> ' . TicketModule::t('app', 'Details'), ['view', 'id' => $ticket->ticket_id], ['class' => 'btn btn-primary']) ?>
                                        <?= Html::a('<span class="glyphicon glyphicon-zoom-in"></span> ' . TicketModule::t('app', 'Observations'), ['observation', 'id' => $ticket->ticket_id], ['class' => 'btn btn-info']) ?>
                                    </div>

                                </div>

                            </div>
                            <!-- end Customer ticket -->

                        <?php endforeach; ?>

                    </div>
                </div>
            </div>
            <!-- end Customer panel -->

        <?php endif; endforeach; ?>

    <?php else : ?>

        <p>No hay tickets abiertos.</p>

    <?php endif; ?>

</div>
