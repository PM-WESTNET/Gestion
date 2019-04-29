<?php

use yii\helpers\Html;
use app\modules\config\ConfigModule;

/* @var $this yii\web\View */
/* @var $model app\modules\config\models\Category */

$this->title = ConfigModule::t('config', 'Update {modelClass}: ', [
    'modelClass' => ConfigModule::t('config','Category'),
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Config Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->category_id]];
$this->params['breadcrumbs'][] = ConfigModule::t('config', 'Update');
?>
<div class="category-update">

    <div class="row">
    	<div class="col-sm-8 col-sm-offset-2">
		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>
    	</div>
    </div>

</div>
