<?php

use yii\helpers\Html;
use \yii\helpers\Url;
use yii\web\User;

/**
 * @var yii\web\View $this
 */
$this->title = Yii::$app->params['web_title'];
?>

<?php if(isset($payment_intention_accountability)): ?>
        <div>
            <a href=<?= Url::toRoute(['westnet/notifications/siro/checker-of-payments'])?> >
                <h2>
                    <span class="label label-danger">Hay <?= count($payment_intention_accountability) ?> intenciones de pagos sin contrastar.</span>
                </h2>
            </a>
        </div>
    <?php endif; ?>
<div class="jumbotron">
    <?php if (!empty(Yii::$app->params['web_logo'])): ?>
        <img alt="<?= Yii::$app->params['web_title']; ?>" src="<?='/'.Yii::$app->params['path'].'/'.Yii::$app->params['web_logo']?>"/>

    <?php endif; ?>

    <h1 class="<?php if (empty(Yii::$app->params['web_title'])) echo 'hidden'; ?>"><?= Yii::$app->params['web_title']; ?> </h1>
</div>

<hr/>

