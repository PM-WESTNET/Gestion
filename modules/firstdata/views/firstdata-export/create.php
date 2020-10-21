<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\firstdata\models\FirstdataExport */

$this->title = Yii::t('app', 'Create Firstdata Export');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Firstdata Exports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="firstdata-export-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
