<?= yii\bootstrap\Html::label(\app\modules\ticket\TicketModule::t('app', "Category"), 'ticket-category'); ?>
<?php
    echo yii\bootstrap\Html::dropDownList('Ticket[category_id]', null, \yii\helpers\ArrayHelper::map($model->categories, 'category_id', 'name'), [
        'encode' => false, 
        'separator' => '<br/>', 
        'prompt' => \app\modules\ticket\TicketModule::t('app', 'Select an option...'),
        'data-ticket-type' => '',
        'class' => 'form-control',
        'id' => 'ticket-category'
        ])
?>