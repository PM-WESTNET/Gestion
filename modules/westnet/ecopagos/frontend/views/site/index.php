<?php

use app\modules\config\models\Config;
use app\modules\westnet\ecopagos\EcopagosModule;
use app\modules\westnet\ecopagos\frontend\helpers\UserHelper;

/**
 * @var View $this
 * **/
$this->title = 'Ecopagos';
//Find cashier and cash register
$cashier = UserHelper::getCashier();
$cashRegister = UserHelper::hasOpenCashRegister();
if ($cashRegister) {
    $cashRegister = UserHelper::getOpenCashRegister();
}
?>
<div class="full-height min-height ecopago-bg valign-wrapper">

    <div class="jumbotron bg-white-transparent padding-full valign width-100 z-depth-2">

        <!-- Alerts -->
        <div class="container no-padding">
            <?php
            $flashes = Yii::$app->getSession()->getAllFlashes();
            foreach ($flashes as $class => $flash):
                if ($class == 'error')
                    $class = 'danger';
                ?>
                <?=
                \yii\bootstrap\Alert::widget([
                    'options' => [
                        'class' => 'alert-' . $class
                    ],
                    'body' => $flash
                ]);
                ?>
            <?php endforeach; ?>

            <!-- Limits alerts -->
            <?php if (UserHelper::getEcopago()->isNearLimit()) : ?>
                <div class="alert alert-info font-size-b z-depth-1">
                    <span class="glyphicon glyphicon-info-sign"></span> <?= EcopagosModule::t('app', 'This ecopago branch is near its cash limit. Please consider making a batch closure for remove this limitation.'); ?>
                </div>
            <?php endif; ?>
            <!-- end Limits alerts -->

            <!-- Cash register alerts -->
            <?php if (empty($cashRegister)) : ?>

                <div class="alert alert-danger font-size-b z-depth-1">
                    <span class="glyphicon glyphicon-warning-sign"></span> <?= EcopagosModule::t('app', 'Open cash register to process payouts'); ?>
                </div>

            <?php endif; ?>
            <?php if (!empty($cashRegister) && $cashRegister->isOld()) : ?>

                <div class="alert alert-warning font-size-b z-depth-1">
                    <span class="glyphicon glyphicon-warning-sign"></span> <?= EcopagosModule::t('app', 'Cash register is open, but it is old. Please close this cash register and open one for today for processing new payouts'); ?>
                </div>

            <?php endif; ?>
            <!-- end Cash register alerts -->

        </div>
        <!-- end Alerts -->

        <!-- Date -->
        <div> 

            <span class="label label-default margin-right-half">
                <?= date('d/m/Y'); ?> 
            </span>
            <!-- end Date -->

            <!-- User & Ecopago branch info -->
            <?php if (!empty($cashier)) : ?>

                <span class="label label-info margin-right-half">
                    <?= EcopagosModule::t('app', 'Cashier'); ?> | 
                    <?= $cashier->name; ?> <?= $cashier->lastname; ?>
                    (<?= $cashier->user->username; ?>)
                </span>
                <span class="label label-primary">
                    <?= EcopagosModule::t('app', 'Ecopago branch'); ?> | 
                    <?= $cashier->ecopago->name; ?>
                </span>

            <?php endif; ?>

        </div>
        <!-- end User & Ecopago branch info -->

        <h1 class="font-brand font-size-megatitles font-sans font-shadow small"><?= EcopagosModule::t('app', 'Bienvenido a Ecopagos!'); ?></h1>
        <p class="font-size-xb text-muted"><?= EcopagosModule::t('app', 'Click on any option to continue'); ?></p>

        <!-- Options -->
        <div>

            <!-- Open cash register -->
            <?php if ($cashRegister) : ?>    

                <a class="btn btn-<?= ($cashRegister->date < date('Y-m-d')) ? 'danger' : 'success'; ?> disabled margin-top-quarter" href="#!" role="button">
                    <span class="glyphicon glyphicon-briefcase"></span><strong>
                    <?= EcopagosModule::t('app', 'Cash register open'); ?>:
                    <?= date('d/m/Y H:m:i', $cashRegister->datetime); ?>
                    </strong>
                </a>

            <?php else : ?>

                <a class="btn btn-success margin-top-quarter" href="<?= \yii\helpers\Url::to(['daily-closure/open-cash-register']); ?>" role="button">
                    <span class="glyphicon glyphicon-briefcase"></span> <?= EcopagosModule::t('app', 'Open cash register'); ?>
                </a>

            <?php endif; ?>
            <!-- end Open cash register -->

            <!-- Payouts -->  

            <div class="btn-group">
                <a class="btn btn-primary dropdown-toggle margin-top-quarter <?= (!empty($cashRegister) && $cashRegister->isOld()) ? 'disabled' : ''; ?>" data-toggle="dropdown" href="#" role="button">
                    <?= EcopagosModule::t('app', 'Payouts'); ?> <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">

                    <?php if ($cashRegister) : ?>  
                        <li>
                            <a href="<?= \yii\helpers\Url::to(['payout/create']); ?>">
                                <?= EcopagosModule::t('app', 'Create Payout'); ?>
                            </a>
                        </li>
                        <li role="separator" class="divider"></li>
                    <?php endif; ?>
                    <li>
                        <a href="<?= \yii\helpers\Url::to(['payout/index']); ?>">
                            <?= EcopagosModule::t('app', 'View created payouts'); ?>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- end Payouts -->

            <div class="btn-group">
                <a class="btn btn-info dropdown-toggle margin-top-quarter" data-toggle="dropdown" href="#" role="button">
                    <?= EcopagosModule::t('app', 'Daily closures'); ?> <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="<?= \yii\helpers\Url::to(['daily-closure/index']); ?>">
                            <?= EcopagosModule::t('app', 'View daily closures'); ?>
                        </a>
                    </li>
                    <!-- Daily closures --> 
                    <?php if ($cashRegister) : ?>   
                        <li role="separator" class="divider"></li>
                        <li>
                            <a href="<?= yii\helpers\Url::to(['daily-closure/preview']); ?>">
                                <?= EcopagosModule::t('app', 'Execute daily closure'); ?>
                            </a>
                        </li>
                    <?php endif; ?>
                    <!-- end Daily closures -->
                </ul>
            </div>


            <!-- Batch closures -->            
            <div class="btn-group">
                <a class="btn btn-danger dropdown-toggle margin-top-quarter" data-toggle="dropdown" href="#" role="button">
                    <?= EcopagosModule::t('app', 'Batch closures'); ?> <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="<?= \yii\helpers\Url::to(['batch-closure/create']); ?>">
                            <?= EcopagosModule::t('app', 'Execute batch closure'); ?>
                        </a>
                    </li>
                    <li role="separator" class="divider"></li>
                    <li>
                        <a href="<?= \yii\helpers\Url::to(['batch-closure/index']); ?>">
                            <?= EcopagosModule::t('app', 'View all batch closures'); ?>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- end Batch closures -->

            <!-- Cashier options -->        
            <div class="btn-group">
                <a class="btn btn-default dropdown-toggle margin-top-quarter" data-toggle="dropdown" href="#" role="button">
                    <?= EcopagosModule::t('app', 'Cashier options'); ?> <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">    
                    <!-- Password -->                
                    <li>
                        <a href="<?= \yii\helpers\Url::to(['cashier/change-password']); ?>">
                            <span class="glyphicon glyphicon-lock"></span> <?= EcopagosModule::t('app', 'Change password'); ?>
                        </a>
                    </li>
                    <!-- end Password -->
                    <li role="separator" class="divider"></li>
                    <!-- Logout -->
                    <li>
                        <a href="<?= \yii\helpers\Url::to(['/user-management/auth/logout']); ?>">
                            <span class="glyphicon glyphicon-log-out"></span> <?= EcopagosModule::t('app', 'Change cashier'); ?>
                        </a>
                    </li>
                    <!-- end Logout -->
                </ul>
            </div>
            <!-- end Cashier options -->

            <!-- Credential reprint -->
            <div class="btn-group">
                <a class="btn btn-default dropdown-toggle margin-top-quarter" href="<?= \yii\helpers\Url::to(['credential/reprint-ask']); ?>" role="button">
                    <?= EcopagosModule::t('app', 'Credential reprint'); ?>
                </a>
            </div>
            <!-- end Credential reprint -->
            
            <div class="btn-group">
            <a href="<?= \yii\helpers\Url::to(['site/print-instructions'])?>" target="_blank" class="btn btn-default margin-top-quarter">Extension para impresiones</a>
            </div>

            <?php if (webvimark\modules\UserManagement\models\User::canRoute(['/sale/customer/sell'])) { ?>
                <!-- Backend access -->
                <div class="btn-group">
                    <a href="<?= \yii\helpers\Url::to(['/site/index']); ?>" target="_blank" class="btn btn-default dropdown-toggle margin-top-quarter">
                        <span class="glyphicon glyphicon-home"></span> <?= Yii::t('app', 'Administration'); ?>
                    </a>
                </div>
                <!-- end Backend access -->
            <?php } ?>
                
                
        </div>
        <!-- end Options -->

    </div>

</div>

<!-- Print Modal -->
<div class="modal fade" id="print-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="text-primary glyphicon glyphicon-print"></span>
                    <?= EcopagosModule::t('app', 'Printing ticket'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <?= EcopagosModule::t('app', 'Please wait until the ticket is completely printed.'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal"><?= EcopagosModule::t('app', 'Close') . ' (ESC)' ?></button>
            </div>
        </div>
    </div>
</div>
<!-- end Print Modal -->

<script>
    var IndexView = new function(){
        var self = this;

        this.init = function(){
            if ('<?php echo (isset($from) ? $from : '*') ?>' === 'close') {
                IndexView.print();
            }
        }

        this.print = function() {
            $('#print-modal').modal();
            setTimeout(function(){
                Payout.printTicket(<?php if(isset($print)){echo $print;}else{ echo '{}';}?>, 'IndexView.printCallback', '<?= Config::getConfig('chrome_print_app')->value; ?>');
            }, 500);

        }

        this.printCallback = function(response) {
            if((response && (response.status !== 'success') )) {
                alert('Ocurrio un error al imprimir');
            }
            $('#print-modal').modal('hide');
        }
    };
</script>
<?php $this->registerJs('IndexView.init()');?>