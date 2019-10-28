<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 28/10/19
 * Time: 12:09
 */
$this->title = Yii::t('app','Closed Tickets per User')
?>

<div class="tickets-per-user">

    <h1 class="title"><?php echo $this->title?></h1>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo Yii::t('app','Filters')?></h3>
        </div>
        <div class="panel-body">
            <?php $form= \yii\bootstrap\ActiveForm::begin(['method' => 'GET'])?>
            <div class="row">
                <div class="col-lg-4">
                    <?php echo $form->field($search, 'user_id')->widget(\kartik\select2\Select2::class, [
                        'data' => $user_filter,
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'allowedClear' => true
                        ]
                    ])?>
                </div>
                <div class="col-lg-4">
                    <?php echo $form->field($search, 'close_from_date')->widget(\kartik\date\DatePicker::class, [
                        'pluginOptions' => [
                            'format' => 'dd-mm-yyyy'
                        ]
                    ])?>
                </div>
                <div class="col-lg-4">
                    <?php echo $form->field($search, 'close_to_date')->widget(\kartik\date\DatePicker::class, [
                        'pluginOptions' => [
                            'format' => 'dd-mm-yyyy'
                        ]
                    ])?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <?php echo \yii\bootstrap\Html::submitButton('<span class="glyphicon glyphicon-search"></span>'. Yii::t('app','Search'), ['class' => 'btn btn-primary'])?>
                </div>
            </div>
            <?php \yii\bootstrap\ActiveForm::end()?>
        </div>
    </div>

    <?php
        echo \yii\grid\GridView::widget([
            'dataProvider' => $users,
            'columns' => [
                'username',
                [
                    'label' => Yii::t('app','Closed Tickets'),
                    'value' => function ($model) use ($search){
                        return $search->ticketsPerUser($model->id);
                    }
                ],
            ]
        ])

    ?>




</div>
