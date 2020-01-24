<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\Account */

$this->title = $model->name;
?>
<div class="account-form col-lg-6">
    <h3><?= Html::encode($this->title) ?></h3>

    <p>
        <?= Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->account_id], ['class' => 'btn btn-primary']) ?>
        <?php if($model->deletable) {
            echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), "#", [
                'class' => 'btn btn-danger',
                'onClick' => 'parent.Account.delete(this)',
                'data' => [
                    'id' => $model->account_id
                ],]);
            }
        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'account_id',
            [
                'attribute' => 'parentAccount.name',
                'label'     => Yii::t('accounting', 'Parent Account')
            ],
            'name',
            'code',
            [
                'attribute' => 'status',
                'value' => function($model) {
                    if ($model->status === \app\modules\accounting\models\Account::ENABLED_STATUS){
                        return Yii::t('app', 'Active');
                    }

                    return Yii::t('app', 'Disabled');
                }
            ],
            'is_usable:boolean',
        ],
    ]) ?>

</div>
