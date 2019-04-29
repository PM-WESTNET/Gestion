<?php
isset($old) ? $old = $old : $old = false;
?>
<div class="event-note bg-info padding-quarter no-margin-bottom">
    
    <span class="label label-info"><?= Yii::$app->formatter->asDatetime($time); ?></span>
    <span class="label label-primary"><?= $username; ?></span>
    
    <?php if (!$old) : ?>
        <span data-observation="delete" class="label label-danger clickable">Eliminar</span>
        <input type="hidden" name="Ticket[observations][<?= $time; ?>][title]" value="<?= $title; ?>" />
        <input type="hidden" name="Ticket[observations][<?= $time; ?>][content]" value="<?= $body; ?>" />
    <?php endif; ?>
        
    <p class="font-bold padding-quarter no-margin-bottom">
        <?= $title; ?>
    </p>
    <p class="padding-quarter no-margin-top no-margin-bottom text-muted">
        <?= $body; ?>
    </p>
    
</div>