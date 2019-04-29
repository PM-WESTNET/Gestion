<?php
use yii\widgets\LinkPager; 
?>
<ul class="list-group">

    <li class="list-group-item">
        <div class="row padding-bottom-quarter">
            <div class="col-lg-2">
                <span class="glyphicon glyphicon glyphicon-flag"></span> 
                <strong>Identificación</strong> 
            </div>
            <div class="col-lg-10">
                <span class="display-inline-block font-white label label-default" style="background-color: <?= $model->color->color; ?>">
                    [<?= $model->number; ?>]
                </span>
            </div>
        </div>
        <div class="row padding-bottom-quarter">
            <div class="col-lg-2">
                <span class="glyphicon glyphicon-exclamation-sign"></span> 
                <strong>Ticket</strong> 
            </div>
            <div class="col-lg-10">
                <span class="text-primary"><?= $model->title; ?></span>
            </div>
        </div>
        <div class="row padding-bottom-quarter">
            <div class="col-lg-2">
                <span class="glyphicon glyphicon-tag"></span> 
                <strong><?= $model->getAttributeLabel('category_id'); ?></strong>
            </div>
            <div class="col-lg-10">
                <?= $model->category->name; ?>
            </div>
        </div>
        <div class="row padding-bottom-quarter">
            <div class="col-lg-2">
                <span class="glyphicon glyphicon-tasks"></span> 
                <strong><?= $model->getAttributeLabel('status'); ?></strong>
            </div>
            <div class="col-lg-10">
                <span class="text-success"><?= $model->status->name; ?></span>
            </div>
        </div>
        <div class="row padding-bottom-quarter">
            <div class="col-lg-2">
                <span class="glyphicon glyphicon-user"></span> 
                <strong><?= $model->getAttributeLabel('customer_id'); ?></strong>
            </div>
            <div class="col-lg-10">
                <?= $model->customer->name; ?>
            </div>
        </div>
        <div class="row padding-bottom-quarter">
            <div class="col-lg-2">
                <span class="glyphicon glyphicon-time"></span> 
                <strong><?= \app\modules\ticket\TicketModule::t('app', 'Created on'); ?></strong>
            </div>
            <div class="col-lg-10">
                <?= Yii::$app->formatter->asDatetime($model->start_datetime); ?>
            </div>
        </div>
        <div class="row padding-bottom-quarter">
            <div class="col-lg-2">
                <span class="glyphicon glyphicon-user"></span>
                <strong><?= $model->getAttributeLabel('user_id'); ?></strong>
            </div>
            <div class="col-lg-10">
                <?= $model->user->username; ?>
            </div>
        </div>
    </li>

    <li class="list-group-item list-group-item-text">
        <span class="text-primary font-bold display-block"><?= $model->getAttributeLabel('content'); ?></span>
        <p class="font-italic">
            <?= $model->content; ?>                        
        </p>
    </li>

    <!-- Observations -->
    <?php
        $currentObservations = (!empty($observations)) ? $observations : array_slice($model->observations, 0, 5) ;
    ?>
    <?php if (!empty($currentObservations)) : ?>
    
        <li class="list-group-item">  

            <span class="text-primary font-bold display-block margin-bottom-half">
                <span class="glyphicon glyphicon-zoom-in"></span> <?= \app\modules\ticket\TicketModule::t('app', 'Observations') ?>
            </span>

            <?php foreach ($currentObservations as $keyObs => $obs) : ?>
                <div class="row">
                    <div class="col-lg-12">

                        <div class="panel panel-<?= (empty($keyObs)) ? 'primary' : 'info'; ?>">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                    #<?= $obs->order; ?> | 
                                    <strong><?= $obs->title; ?></strong>

                                </h3>
                            </div>
                            <div class="panel-body">
                                <?= $obs->description; ?>
                            </div>
                            <div class="panel-footer">
                                <small class="text-muted">
                                    <?= \app\modules\ticket\TicketModule::t('app', 'Created on'); ?> <?= Yii::$app->formatter->asDatetime($obs->datetime); ?>
                                    <?= \app\modules\ticket\TicketModule::t('app', 'by'); ?> <?= $obs->user->username; ?>
                                </small>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (!empty($pages)) : ?>
                <?=
                    LinkPager::widget([
                        'pagination' => $pages,
                    ]);
                ?>
            <?php else : ?>
            
            <p class="margin-top-half font-size-sm text-muted">Mostrando últimas 5 observaciones</p>
            
            <?php endif; ?>

        </li>
    <?php endif; ?>
    <!-- end Observations -->

</ul>