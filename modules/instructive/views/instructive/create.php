<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\instructive\models\Instructive */

$this->title = Yii::t('app','Create Instructive');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Instructives'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="instructive-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'instructiveCategories' => $instructiveCategories,
    ]) ?>

</div>
