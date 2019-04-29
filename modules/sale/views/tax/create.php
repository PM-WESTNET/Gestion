<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\Tax */

$this->title = Yii::t('app', 'Create {modelClass}', ['modelClass' => Yii::t('app','Tax')]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Taxes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tax-create">

    <div class="row">
    	<div class="col-sm-8 col-sm-offset-2">
		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>
    	</div>
    </div>

</div>
