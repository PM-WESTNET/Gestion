<?php

use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use yii\helpers\Html;
/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\Bill $model
 */
// This is done to show to the user all the flashes that were displayed before the redirect and lost after it.
if(yii::$app->session->has('customFlashes')) {

    $flashes = yii::$app->session['customFlashes']; //Previously saved on the controller before the redirect
    yii::$app->session->remove('customFlashes'); // unset

    function recursiveAddFlashes($flashes,$depthLvl = 0){
        foreach($flashes as $key => $flashMsg){
            // array logic
            if(is_array($flashMsg)){
                recursiveAddFlashes($flashMsg,$depthLvl+1);
            }
            // string logic
            else{
                Yii::$app->session->addFlash('error', $flashMsg);
            }
        }
    }
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
