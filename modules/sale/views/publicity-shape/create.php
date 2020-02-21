<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\PublicityShape */

$this->title = Yii::t('app', 'Create Publicity Shape');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Publicity Shapes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="publicity-shape-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
