<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\ProfileClass $model
 */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
  'modelClass' => Yii::t('app','Profile Class'),
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Profile Classes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->profile_class_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="profile-class-update">

    <div class="row">
    	<div class="col-sm-8 col-sm-offset-2">
		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>
    	</div>
    </div>

</div>
