<?php

$this->title = Yii::t('app','Infobip Sended Messages');
$this->params['breadcrumbs'][]= $this->title;
?>

<div class="sended-messages">

    <h1 class="title"><?php echo $this->title?></h1>

    <?php echo \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $search,
        'columns' => [
            ['class' => \yii\grid\SerialColumn::class],

            [
                'attribute' => 'sent_timestamp',
                'format' => 'datetime',
                'filter' => \kartik\date\DatePicker::widget([
                    'name' => 'InfobipMessageSearch[sent_timestamp]',
                    'value' => $search->sent_timestamp,
                    'pluginOptions' => [
                        'format' => 'dd-mm-yyyy'
                    ]
                ]),
                'options' => ['width' => '300px']
            ],
            [
                'attribute' => 'customer_id',
                'value' => function($model) {
                    if ($model->customer){
                        return $model->customer->fullName;
                    }

                    return null;
                },
                'filter' => $this->render('@app/modules/sale/views/customer/_find-with-autocomplete', ['model' => $search, 'attribute' => 'customer_id']),
                'options' => ['width' => '300px']
            ],
            'to',
            'status',
            'message'
        ]
    ])?>
</div>
