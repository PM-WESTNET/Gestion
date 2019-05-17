<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\AdsPercentagePerCompany */

$this->title = Yii::t('app', 'Create Ads Percentage Per Company');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ads Percentage Per Companies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ads-percentage-per-company-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
