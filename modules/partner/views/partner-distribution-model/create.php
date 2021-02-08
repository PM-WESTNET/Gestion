<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\partner\models\PartnerDistributionModel */

$this->title = Yii::t('app', 'Create {modelClass}', ['modelClass'=> Yii::t('partner', 'Partner Distribution Model')]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('partner', 'Partner Distribution Models'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partner-distribution-model-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
