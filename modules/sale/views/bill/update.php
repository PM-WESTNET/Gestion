<?php

use yii\helpers\Html;
/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\Bill $model
 */
// This is done to show to the user all the flashes that were displayed before the redirect and lost after it.
$flashes=yii::$app->session['customFlashes']; //Previously saved on the controller before the redirect

if(!empty($flashes)){
    foreach ($flashes as $key => $flashType){ // get flash type: error/danger/etc
        if(is_array($flashType)){ // if the flash is array
            foreach ($flashType as $flashMsg){ // re-create the flash message
                Yii::$app->session->addFlash($key, $flashMsg);
            }   
        }else{ // if the flash is only a string
            Yii::$app->session->addFlash($key, $flashType);
        }
    }
}else{
    Yii::$app->session->addFlash('info','No flashes found');
}

$this->title = Yii::t('app', '{modelClass}: ', [
  'modelClass' => $model->typeName,
]) . $model->bill_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bills'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->bill_id, 'url' => ['view', 'id' => $model->bill_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="bill-update">

    <div class="row">
        <div class="col-sm-12 ">
            <?php if(!$embed): ?>
            <h1><?= $model->typeName ?></h1>
            <?php endif; ?>
            
            <?= $this->render('_form', [
                'model' => $model,
                'productSearch' => $productSearch,
                'dataProvider' => $dataProvider,
                'embed' => $embed,
                'electronic_billing' => $electronic_billing
            ]) ?>
        </div>
    </div>

</div>
