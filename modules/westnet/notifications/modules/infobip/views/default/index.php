<?php

$this->title = Yii::t('app','Infobip Response Messages');

?>



<div class="infobip-default-index">
    <h1><?= $this->title ?></h1>

    <?php echo \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => \yii\grid\SerialColumn::class],

            'from',
            'to',
            'content',
            'received_timestamp:datetime'
        ]
    ])?>
</div>
