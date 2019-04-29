<?php

use yii\helpers\Html;
use app\modules\config\ConfigModule;

/* @var $this yii\web\View */
/* @var $model app\modules\config\models\Rule */

$this->title = ConfigModule::t('config', 'Update {modelClass}: ', [
    'modelClass' => 'Rule',
]) . ' ' . $model->rule_id;
$this->params['breadcrumbs'][] = ['label' => ConfigModule::t('config', 'Rules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->rule_id, 'url' => ['view', 'id' => $model->rule_id]];
$this->params['breadcrumbs'][] = ConfigModule::t('config', 'Update');
?>
<div class="rule-update">

    <div class="row">
    	<div class="col-sm-8 col-sm-offset-2">
		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>
    	</div>
    </div>

</div>
