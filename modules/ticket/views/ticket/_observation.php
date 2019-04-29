<?php

?>

<div class="row">
    <div class="col-xs-12">
        <h4><?php echo $model->title?></h4>
        <small><?php echo $model->user->username . ' - '. Yii::$app->formatter->asDatetime($model->datetime)?></small>

        <p><?php echo $model->description?></p>
    </div>
</div>
<hr>
