<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\ProductCommission */

$this->title = Yii::t('app', 'Create {modelClass}', ['modelClass' => Yii::t('westnet', 'Product Commission')]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('westnet', 'Product Commissions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-commission-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
