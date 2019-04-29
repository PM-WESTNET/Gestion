<?php

use yii\helpers\Html;
use app\modules\config\ConfigModule;

/* @var $this yii\web\View */
/* @var $model app\modules\config\models\Item */

$this->title = ConfigModule::t('config', 'Create {modelClass}', ['modelClass' => ConfigModule::t('config', 'Item')]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Config Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-create">

    <div class="row">
    	<div class="col-sm-8 col-sm-offset-2">
		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>
    	</div>
    </div>

</div>
