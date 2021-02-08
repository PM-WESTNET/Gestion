<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 14/08/19
 * Time: 17:36
 */
use yii\widgets\ActiveForm;


$search = new \app\modules\accounting\models\search\ResumeSearch()
?>

<div class="panel panel-search" style="width: 85%">
    <div class="panel-body">
        <?php
        $form= ActiveForm::begin(['id' => 'resume_search_form'])
        ?>
        <div class="row">
            <div class="col-lg-4">
                <?php
                    echo $form->field($search, 'date')->widget(\kartik\date\DatePicker::class, [
                        'pluginOptions' => [
                            'format' => 'dd-mm-yyyy',
                            'autoclose' => true,
                        ],
                        'size' => 'sm'

                    ])?>
            </div>
            <div class="col-lg-4">
                <?php
                    echo $form->field($search, 'operation_type')->widget(\kartik\select2\Select2::class, [
                        'data' => $operationTypes,
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        'size' => \kartik\select2\Select2::SIZE_SMALL,
                        'options' => ['placeholder' => Yii::t('app','Select an Operation Type')]
                    ])
                ?>
            </div>
            <div class="col-lg-3">
                <?php
                    echo $form->field($search, 'description')->textInput(['class' => 'form-control input-sm']);
                ?>
            </div>
            <div class="col-lg-1">
                <br>
                <?php echo \yii\helpers\Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-sm btn-default', 'id' => 'btn-resume-search' ])?>
            </div>
            <?php echo $form->field($search, 'resume_id', ['template' => '{input}'])->hiddenInput(['value' => $resume_id])?>
            <?php ActiveForm::end()?>
        </div>

    </div>
</div>
