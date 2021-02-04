<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\AccountMovement */

$this->title = Yii::t('app', 'Create {modelClass}', ['modelClass'=>Yii::t('accounting', 'Manual Entry')]) .' - '. $box->account->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('accounting', $box->account->name), 'url' => ['/accounting/money-box-account/daily-box-movements', 'id' => $box->money_box_account_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-movement-create">

    <div class="row">
    	<div class="col-sm-12">
            <h1><?= Html::encode($this->title) ?> <small><?= $model->date ?></small></h1>
            
		    <?= $this->render('_form-daily-box', [
		        'model' => $model,
                'box' => $box,
                'item' => $item
		    ]) ?>
    	</div>
    </div>

</div>
