<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\BillType */

$this->title = Yii::t('app', 'Create {modelClass}', ['modelClass'=>Yii::t('app','Bill Type')]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bill Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bill-type-create">

    <div class="row">
    	<div class="col-sm-8 col-sm-offset-2">
		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>
    	</div>
    </div>

</div>
