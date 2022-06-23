<?php

use yii\helpers\Html;
use \yii\helpers\Url;
use yii\web\User;
use webvimark\modules\UserManagement\models\User as UserManagement;

/**
 * @var yii\web\View $this
 */
$this->title = Yii::$app->params['web_title'];
?>

<div class="jumbotron">
    <?php if (!empty(Yii::$app->params['web_logo'])): ?>
        <img class="company-logo" alt="<?= Yii::$app->params['web_title']; ?>" src="<?='/'.Yii::$app->params['path'].'/'.Yii::$app->params['web_logo']?>"/>

    <?php endif; ?>
</div>

<hr/>

<?php if( isset($payment_intention_accountability) && UserManagement::canRoute(['westnet/notifications/siro/checker-of-payments']) ): ?>
        <div>
            <a href=<?= Url::toRoute(['westnet/notifications/siro/checker-of-payments'])?> >
                <h2>
                    <span class="label label-danger">Hay <?= count($payment_intention_accountability) ?> intenciones de pagos sin contrastar.</span>
                </h2>
            </a>
        </div>
<?php endif; ?>

<!-- If user can route to the closing bills view, show them the bill count of draft bills -->
<?php if( isset($bill_errors_count) && UserManagement::canRoute(['sale/batch-invoice/close-invoices-index']) ): ?>
        <div>
            <a href=<?= Url::toRoute(['sale/bill',])?> >
                <h2>
                    <span class="label label-danger">Hay <?= $bill_errors_count ?> facturas sin cerrar.</span>
                </h2>
            </a>
        </div>
<?php endif; ?>