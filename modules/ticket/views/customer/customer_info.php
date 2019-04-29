<!-- Customer info -->
<?php
use app\modules\ticket\TicketModule;
use yii\bootstrap\Html;
use yii\helpers\Url;
?>
<div class="panel panel-default">

    <div class="panel-heading">

        <h3 class="panel-title font-bold">
            <?= TicketModule::t('app', 'Information about'); ?>:  <span class="a"><?= Html::a($model->fullName, Url::to(['/sale/customer/view', 'id'=> $model->customer_id])) ?></span>
        </h3>

    </div>

    <div class="panel-body">
        <ul class="list-group no-margin-bottom">
            <li class="list-group-item">
                <span class="badge">
                    <?=  $model->code ?>
                </span>
                <?= $model->getAttributeLabel('code'); ?>
            </li>
            <li class="list-group-item">
                <span class="badge">
                    <?= ($model->address) ? $model->address : TicketModule::t('app', 'No information') ; ?>
                </span>
                <?= $model->getAttributeLabel('address'); ?>
            </li>
            <li class="list-group-item">
                <span class="badge">
                    <?= ($model->email) ? $model->email : TicketModule::t('app', 'No information') ; ?>
                </span>
                <?= $model->getAttributeLabel('email'); ?>
            </li>
            <li class="list-group-item">
                <span class="badge">
                    <?= ($model->phone) ? $model->phone : TicketModule::t('app', 'No information') ; ?>
                </span>
                <?= $model->getAttributeLabel('phone'); ?>
            </li>
            <li class="list-group-item">
                <span class="badge">
                    <?= ($model->phone2) ? $model->phone2 : TicketModule::t('app', 'No information') ; ?>
                </span>
                <?= $model->getAttributeLabel('phone2'); ?>
            </li>
            <li class="list-group-item">
                <span class="badge">
                    <?= ($model->phone3) ? $model->phone3 : TicketModule::t('app', 'No information') ; ?>
                </span>
                <?= $model->getAttributeLabel('phone3'); ?>
            </li>
        </ul>
    </div>

</div>
<!-- end Customer info -->