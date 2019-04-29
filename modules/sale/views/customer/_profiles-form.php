<?php
$profileClasses = app\modules\sale\models\Customer::getEnabledProfileClasses();
?>

<?php foreach($profileClasses as $class): ?>

    <?= $form->field($model,$class->attr)->{$class->data_type}()->hint($class->hint); ?>

<?php endforeach; ?>
