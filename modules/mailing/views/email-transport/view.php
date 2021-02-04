<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\EmailTransport */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Email Transports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-transport-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->email_transport_id], ['class' => 'btn btn-primary']) ?>
        <?php if($model->deletable) echo Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->email_transport_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        <button class="btn btn-default test-transport" data-id="<?= $model->email_transport_id ?>" data-toggle="modal" data-target="#modalTest"><span class="glyphicon glyphicon-envelope"></span> Test</button>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'email_transport_id:email',
            'name',
            'from_email:email',
            'transport',
            'host',
            'port',
            'username',
            'password',
            'encryption',
            'layout',
            'relation_class',
            [
                    'attribute' => 'relation_id',
                'value' => $model->getText()
            ]
        ],
    ]) ?>
</div>
<?= $this->render('_test'); ?>
