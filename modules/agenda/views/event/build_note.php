<?php
isset($old) ? $old = $old : $old = false;
?>
<div class="event-note bg-info padding-quarter no-margin-bottom">
    
    <span class="label label-info"><?= Yii::$app->formatter->asDatetime($time); ?></span>
    <span class="label label-primary"><?= $username; ?></span>
    
    <?php if (!$old) : ?>
        <span data-event="delete" class="label label-danger clickable">Eliminar</span>
        <input type="hidden" name="Task[events][<?= $time; ?>]" value="<?= $body; ?>" />
    <?php endif; ?>
        
    <p class="padding-quarter no-margin-bottom">
        <?= $body; ?>
    </p>
    
</div>