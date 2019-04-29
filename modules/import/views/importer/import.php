<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 */

$this->title = Yii::t('import', 'Import', []);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-import">
	<div class="title">
    	<h1><?= Html::encode($this->title) ?></h1>		
	</div>
	
	<div class="row">
		<div class="col-sm-6 col-sm-offset-3">
		    <?= $this->render('_form', [
		        'importers' => $importers,
		        'model'=>$model
		    ]) ?>			
		</div>
	</div>

</div>
