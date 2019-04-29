<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\AccountConfig */

$this->title = Yii::t('app', 'Create {modelClass}', ['modelClass'=>Yii::t('accounting','Account Config')]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('accounting', 'Account Configs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-config-create">

	<div class="row">
    	<div class="col-sm-12">
		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		        'dataProvider' =>$dataProvider
		    ]) ?>
    	</div>
    </div>
    
</div>
