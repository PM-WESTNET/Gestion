<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\instructive\models\Instructive */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Instructives', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="instructive-view">

    <?php if (\webvimark\modules\UserManagement\models\User::hasRole('superadmin')):?>
        <p>
            <?= Html::a('Update', ['update', 'id' => $model->instructive_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a('Delete', ['delete', 'id' => $model->instructive_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    <?php endif?>
    <h1 style="text-align: center;"><?= Html::encode($this->title) ?></h1>

    <?php echo $model->content?>

</div>
