<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\firstdata\models\FirstdataCompanyConfig */

$this->title = Yii::t('app', 'Create Firstdata Company Config');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Firstdata Company Configs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="firstdata-company-config-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'companies' => $companies,
    ]) ?>

</div>
