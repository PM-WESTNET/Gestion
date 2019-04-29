<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\provider\models\ProviderBill */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => Yii::t('app', 'Provider Bill'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Provider Bills'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provider-bill-create">

    <div class="row">
    	<div class="col-sm-8 col-sm-offset-2">
		    <h1>
                <?= Html::encode($this->title) ?> 
                <small><?= ($model->provider ? $model->provider->name : "" )?></small>
            </h1>

		    <?= $this->render('_form', [
		        'model' => $model,
                'dataProvider'=>$dataProvider,
                'itemsDataProvider' => $itemsDataProvider,
                'from' => $from
		    ]) ?>

            
    	</div>
    </div>

</div>
