<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\ProfileClass $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
  'modelClass' => Yii::t('app','Profile Class'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Profile Classes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profile-class-create">

    <div class="row">
    	<div class="col-sm-8 col-sm-offset-2">
		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>
    	</div>
    </div>

</div>
