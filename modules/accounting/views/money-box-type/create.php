<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\MoneyBoxType */

$this->title = Yii::t('app', 'Create {modelClass}', ['modelClass'=>Yii::t('accounting','Money Box Types')]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('accounting', 'Money Box Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="money-box-type-create">

    <div class="row">
    	<div class="col-sm-8 col-sm-offset-2">
		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>
    	</div>
    </div>

</div>
