<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\partner\models\Partner */

$this->title = Yii::t('app', 'Create {modelClass}', ['modelClass'=> Yii::t('partner', 'Partner')]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('partner', 'Partners'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partner-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
