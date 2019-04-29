<?php

use yii\helpers\Html;

$this->params ['breadcrumbs'] [] = [
    'label' => Yii::t('backup', 'Manage'),
    'url' => array(
        'index'
    )
];
$this->params['breadcrumbs'][] = [
    'label' => 'Restore',
    'url' => array('restore'),
];
?>


<h1>
<?php //echo  $this->action->id;  ?>
</h1>

<p>
<?php 
if (isset($error)) {
    echo $error;
} else {
    echo Yii::t('backup','Done');
}
?>
</p>
<p>

    <?= Html::a(Yii::t('backup','View site'), ['index'], ['class' => 'btn btn-warning']) ?>        


<?php //echo Html::link('View Site',Yii::app()->HomeUrl) ?>
</p>
