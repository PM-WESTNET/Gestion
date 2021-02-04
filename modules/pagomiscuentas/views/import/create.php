<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\partner\models\Partner */

$this->title = Yii::t('app', 'Create {modelClass}', ['modelClass'=> Yii::t('pagomiscuentas', 'Importation of Pagomiscuentas')]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pagomiscuentas-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
