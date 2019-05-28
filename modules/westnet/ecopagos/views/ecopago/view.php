<?php

use app\modules\westnet\ecopagos\EcopagosModule;
use yii\helpers\Html;
use yii\widgets\DetailView;
use webvimark\modules\UserManagement\models\User;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\Ecopago */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => EcopagosModule::t('app', 'Payouts in Ecopagos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ecopago-view">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <!-- Options -->
        <p>
            <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('app', 'Update'), ['update', 'id' => $model->ecopago_id], ['class' => 'btn btn-primary']) ?>
            <?php if ($model->status->slug === 'enabled'):?>
                <?= Html::a('<span class="glyphicon glyphicon-copy"></span> ' . EcopagosModule::t('app', 'Manage cashiers'), ['cashier/list-by-ecopago', 'ecopago_id' => $model->ecopago_id], ['class' => 'btn btn-info']) ?>
                <?= Html::a('<span class="glyphicon glyphicon-paste"></span> ' . EcopagosModule::t('app', 'Manage collectors'), ['collectors', 'id' => $model->ecopago_id], ['class' => 'btn btn-info']) ?>
            <?php endif;?>
            <?php if(User::canRoute(['/westnet/ecopagos/ecopago/disable']) && $model->canDisable()):?>
                <?php echo Html::a('<span class="glyphicon glyphicon-remove"></span> '. Yii::t('app','Disable Ecopago'), ['disable', 'id' => $model->ecopago_id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => EcopagosModule::t('app', 'Are you sure you want to disable this ecopago?'),
                        'method' => 'post',
                    ]])?>
            <?php endif;?>

            <?php if($model->deletable) echo Html::a('<span class="glyphicon glyphicon-trash"></span> ' . EcopagosModule::t('app', 'Delete'), ['delete', 'id' => $model->ecopago_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => EcopagosModule::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
        <!-- end Options -->
    </div>
    
    <!-- Alerts -->
    <div class="alerts">
        
        <?php if(empty($model->collectors)) : //Without collectors ?>
            <div class="alert alert-warning">
                <span class="glyphicon glyphicon-warning-sign display-inline-block margin-right-half"></span> 
                <?= EcopagosModule::t('app', 'This Ecopago branch has not have any collector assignated.'); ?> 
            </div>
        <?php endif; ?>
        
        <?php if(empty($model->cashiers)) : //Without collectors ?>
            <div class="alert alert-warning">
                <span class="glyphicon glyphicon-warning-sign display-inline-block margin-right-half"></span> 
                <?= EcopagosModule::t('app', 'This Ecopago branch has not have any cashiers.'); ?> 
            </div>
        <?php endif; ?>
        
    </div>
    <!-- end Alerts -->

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'ecopago_id',
            'address_id',
            [
                'attribute' => 'status',
                'value' => function($model){
                    return Yii::t('app', $model->status->name);
                }
            ],
            'create_datetime:datetime',
            'update_datetime:datetime',
            'name',
            'description:ntext',
            'limit',
            'number',
            [
                'label' => EcopagosModule::t('app', 'Collectors'),
                'value' => $model->fetchCollectors(false)
            ],
            [
                'label' => EcopagosModule::t('app', 'Cashiers'),
                'value' => $model->fetchCashiers(false)
            ],
            [
                'label' => EcopagosModule::t('app', 'Commission'),
                'value' => (!empty($model->activeCommission)) ? $model->commission_value . '('.$model->activeCommission->fetchSymbol().')' : '' ,
            ],
            [
                'label' => Yii::t('app', 'Provider'),
                'value' => ($model->provider_id ? $model->provider->name  : '')
            ],
        ],
    ]) ?>

</div>
