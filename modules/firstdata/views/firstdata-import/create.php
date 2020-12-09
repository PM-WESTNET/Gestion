<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\firstdata\models\FirstdataImport */

$this->title = Yii::t('app', 'Create Firstdata Import');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Firstdata Imports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="firstdata-import-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'accounts' => $accounts
    ]) ?>

</div>
