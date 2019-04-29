<?php
use app\modules\westnet\ecopagos\EcopagosModule;
?>

<!-- Collector login form -->
<div id="collector-info" class="panel panel-default position-relative z-depth-1 z-depth-important" style="z-index: 11;">

    <div class="panel-heading">
        <h3 class="panel-title"><?= EcopagosModule::t('app', 'Information about Collector'); ?></h3>
    </div>
    <div class="panel-body">

        <p class="text-muted"><?= EcopagosModule::t('app', 'Input collector number and password, and then press the Enter key'); ?></p>

        <input type='text' style='display: none'>
        <input type='password' style='display: none'>

        <div class="row">
            <div class="col-lg-6">
                <?=
                $form->field($collector, 'number')->textInput([
                    'maxlength' => 50,
                    'autocomplete' => 'false',
                    'autocomplete' => 'off',
                ])
                ?>
            </div>
            <div class="col-lg-6">
                <?=
                $form->field($collector, 'password')->passwordInput([
                    'maxlength' => 50,
                    'autocomplete' => 'false',
                    'autocomplete' => 'off',
                ])
                ?>
            </div>
        </div>

        <div class="collector-error no-margin-bottom">

        </div>

    </div>

</div>
<!-- end Collector login form -->