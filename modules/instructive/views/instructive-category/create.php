<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\instructive\models\InstructiveCategory */

$this->title = Yii::t('app','Create Instructive Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Instructive Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="instructive-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'roles' => $roles
    ]) ?>

</div>
