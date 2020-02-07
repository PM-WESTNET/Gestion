<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\PublicityShape */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Publicity Shapes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="publicity-shape-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->publicity_shape_id], ['class' => 'btn btn-primary']) ?>
        <?php if($model->getDeletable()) {
            echo Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->publicity_shape_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]);
        }?>

    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'publicity_shape_id',
            'name',
            'slug',
        ],
    ]) ?>

</div>
