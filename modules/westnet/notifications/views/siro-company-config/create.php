<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\westnet\notifications\models\SiroCompanyConfig */

$this->title = 'Create Siro Company Config';
$this->params['breadcrumbs'][] = ['label' => 'Siro Company Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="siro-company-config-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
