<?php

use app\modules\accounting\models\Account;
use app\modules\accounting\models\ResumeItem;
use kartik\select2\Select2;
use kartik\widgets\FileInput;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\Resume */

$name = $model->moneyBoxAccount->moneyBox->name . " - " . $model->moneyBoxAccount->number . ' - ' . $model->name;

$this->title = Yii::t('accounting', 'Resume Detail') . ' - ' . $name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('accounting', 'Resumes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $name, 'url' => ['view', 'id' => $model->resume_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
    <!--<style>
        .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
            padding: 0px;
        }
    </style> -->

    <?php if ($model->moneyBoxAccount->moneyBox->hasUndefinedOperationType()):?>
        <div class="alert alert-warning">
            <?php echo Yii::t('accounting','Money Box has undefined code operations')?>
        </div>
    <?php endif;?>
    <div class="resume-update">
        <input type="hidden" value="<?= $model->resume_id ?>" name="resume_id" id="resume_id"/>
        <h1><?= Html::encode($this->title) ?></h1>
        <?php if ($model->can('closed')): ?>

            <p>

                <?= Html::a('<span class="glyphicon glyphicon-ok"></span> ' . Yii::t('app', 'Ready'), ['change-state', 'id' => $model->resume_id, 'newState' => 'closed'], [
                    'class' => 'btn btn-success',
                    //'style' => 'position:relative; top: -7px;',
                    'data' => [
                        'confirm' => Yii::t('accounting', 'Are you sure you want to close this resume ?'),
                        'method' => 'post',
                    ],]);
                ?>
            </p>

        <?php endif; ?>

        <?php \yii\widgets\Pjax::begin(['id' => 'w_header_resume', 'timeout' => 5000]); ?>
        <div class="row">
            <div class="col-sm-12">

            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <?= Html::encode($this->title) ?>

                    </h3>
                </div>
                <div class="panel-body">

                    <div class="row">
                        <div class="col-sm-5 text-center">
                            <div class="col-sm-4 text-center">
                                <strong><?= Yii::t('app', 'Date'); ?></strong>
                                <br/>
                                <?= $model->date ?>
                            </div>
                            <div class="col-sm-4 text-center">
                                <strong><?= Yii::t('accounting', 'Date From'); ?></strong>
                                <br/>
                                <?= $model->date_from ?>
                            </div>
                            <div class="col-sm-4 text-center">
                                <strong><?= Yii::t('accounting', 'Date To'); ?></strong>
                                <br/>
                                <?= $model->date_to ?>
                            </div>
                        </div>
                        <div class="col-sm-1 text-center">
                            <strong><?= Yii::t('app', 'Status'); ?></strong>
                            <br/>
                            <?= Yii::t('accounting', ucfirst($model->status)) ?>
                        </div>
                        <?php
                        $totals = $model->getTotal();
                        ?>

                        <div class="col-sm-3 text-center">
                            <div class="col-sm-6 text-center">
                                <strong><?= Yii::t('accounting', 'Total Debit'); ?></strong>
                                <br/>
                                <?= Yii::$app->formatter->asCurrency($totals['debit']) ?>
                            </div>
                            <div class="col-sm-6 text-center">
                                <strong><?= Yii::t('accounting', 'Total Credit'); ?></strong>
                                <br/>
                                <?= Yii::$app->formatter->asCurrency($totals['credit']) ?>
                            </div>
                        </div>
                        <div class="col-sm-3 text-center">
                            <div class="col-sm-6 text-center">
                                <strong><?= Yii::t('accounting', 'Initial Balance'); ?></strong>
                                <br/>
                                <?= Yii::$app->formatter->asCurrency($model->balance_initial) ?>
                            </div>
                            <div class="col-sm-6 text-center">
                                <strong><?= Yii::t('accounting', 'Final Balance'); ?></strong>
                                <br/>
                                <?= Yii::$app->formatter->asCurrency($model->balance_final) ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            </div>
        </div>
        <?php \yii\widgets\Pjax::end(); ?>
        <div class="row">
            <div class="col-sm-12">
                <?php $form = ActiveForm::begin(['action' => ['import-resume', 'id' => $model->resume_id], 'options' => ['id'=>'resume_form','enctype' => 'multipart/form-data']]); ?>
                <input type="hidden" value="3" name="Resume[resume_id]" class="form-control" id="resume-resume_id">
                <div class="col-sm-12">
                    <?php echo $form->field($model, 'columns', [
                            'template' =>
                                '<div class="form-group">
                                       {label}
                                       {input}
                                       {error}
                                       <span class="help-block" id="helpBlock">'.Yii::t('accounting','Input the name of columns separated with comma').'</span>
                                 </div>'

                    ])->widget(\kartik\select2\Select2::class, [
                        'data' => ['Fecha' => 'Fecha', 'Descripcion' => 'Descripcion', 'Código' => 'Código', 'Debe' => 'Debe', 'Haber' => 'Haber'],
                        'value' => ['Fecha', 'Descripcion', 'Código', 'Debe', 'Haber'],
                        'maintainOrder' => true,
                        'options' => [
                            'placeholder' => Yii::t('accounting','Columns of File'),
                            'multiple' => true,
                            'aria-describedby'=> 'helpBlock'
                        ],
                        'pluginOptions' => [
                            'tags' => true,
                            'tokenSeparators' => [','],
                            'maximumInputLength' => 10
                        ],
                    ])?>

                </div>
                <div class="col-sm-12">
                    <?= $form->field($model, 'account_id')->widget(Select2::className(),[
                        'data' => yii\helpers\ArrayHelper::map(Account::getForSelect(), 'account_id', 'name' ),
                        'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
                        'pluginOptions' => [
                            'allowClear' => true
                        ]
                    ]);
                    ?>
                </div>
                <div class="col-sm-12">
                    <?= $form->field($model, 'separator')->dropDownList([
                        "," => '"," Coma',
                        ";" => '";" punto y coma',
                         "\t"=> 'Tabulación'
                    ]);
                    ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'file_import')->widget(FileInput::classname(), [
                        'pluginOptions' => [
                            'showPreview' => false,
                            'showCaption' => true,
                            'showRemove' => true,
                            'showUpload' => false,
                            'overwriteInitial' => true,
                        ]]); ?>
                </div>
                <div class="col-sm-3">
                    <label class="control-label">&nbsp;</label>
                    <div class="form-group">
                        <?= Html::a('<span class="glyphicon glyphicon-upload"></span> ' . Yii::t('app', 'Import'), null, [
                            'class' => 'btn btn-success',
                            'id' => 'import-resume',
                            'data' => [
                                'method' => 'post',
                            ],]);
                        ?>
                    </div>
                </div>
                <div class="col-sm-3">
                    <label class="control-label">&nbsp;</label>
                    <div class="form-group">
                        <?= Html::a('<span class="glyphicon glyphicon-download"></span> ' . Yii::t('app', 'Download'), yii\helpers\Url::toRoute(['resume/download-resume']), [
                            'class' => 'btn btn-success',
                            'id' => 'download-resume',
                            'target' => '_blank'
                        ]);
                        ?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <div class="clearfix margin-top-full"></div>
        
        <?php $canClose = $model->can('closed'); ?>

        <div class="clearfix margin-top-full"></div>

        <div class="row">
            <div class="col-sm-12">


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong>
                            <?= Yii::t('accounting', 'Resume Detail') ?>
                        </strong>
                    </div>
                    <div class="panel-body">
                        <?php \yii\widgets\Pjax::begin(['id' => 'w_item_form', 'timeout' => 5000]); ?>
                        <?= GridView::widget([
                            //'layout' => '{items}',
                            'id' => 'wSummary',
                            'dataProvider' => $resumeItems,
                            'columns' => [
                                [
                                    'label' => Yii::t('app', 'Date'),
                                    'attribute' => 'date',
                                    'format' => ['date']
                                ],
                                [
                                    'label' => Yii::t('accounting', 'Operation Type'),
                                    'value' => function ($model){
                                        if ($model->moneyBoxHasOperationType->operationType){
                                            return $model->moneyBoxHasOperationType->operationType->name;
                                        }

                                        return $model->moneyBoxHasOperationType->code;
                                    },
                                ],
                                [
                                    'label' => Yii::t('app', 'Description'),
                                    'attribute' => 'description',
                                ],
                                [
                                    'label' => Yii::t('accounting', 'Debit'),
                                    'value' => function ($model) {
                                        return Yii::$app->formatter->asCurrency($model->debit);
                                    }
                                ],
                                [
                                    'label' => Yii::t('accounting', 'Credit'),
                                    'value' => function ($model) {
                                        return Yii::$app->formatter->asCurrency($model->credit);
                                    }
                                ],
                                [
                                    'header' => Yii::t('app', 'Status'),
                                    'value' => function ($model) {
                                        return Yii::t('accounting', ucfirst($model->status));
                                    }
                                ],
                            ],
                        ]); ?>
            </div>
        </div>
                        <div class="row">
                            <div class="col-sm-3 text-center">
                                <strong><?= Yii::t('accounting', 'Total Debit'); ?></strong>
                                <br/>
                                <?= Yii::$app->formatter->asCurrency($totals['debit']) ?>
                            </div>
                            <div class="col-sm-3 text-center">
                                <strong><?= Yii::t('accounting', 'Total Credit'); ?></strong>
                                <br/>
                                <?= Yii::$app->formatter->asCurrency($totals['credit']) ?>
                            </div>
                            <div class="col-sm-3 text-center">
                                <strong><?= Yii::t('accounting', 'Balance'); ?></strong>
                                <br/>
                                <?= Yii::$app->formatter->asCurrency($totals['credit']-$totals['debit']) ?>
                            </div>
                            <div class="col-sm-3 text-center">
                                <strong><?= Yii::t('accounting', 'Balance').' + ' . Yii::t('accounting', 'Initial Balance'); ?></strong>
                                <br/>
                                <?= Yii::$app->formatter->asCurrency($model->balance_initial + $totals['credit']-$totals['debit']) ?>
                            </div>

                        </div>
                        <?php \yii\widgets\Pjax::end(); ?>
                    </div>
        </div>

    </div>
    <script>
        var ResumeForm = new function () {
            this.init = function () {
                $(document).off("click", ".btnAddResumeItem")
                    .on("click", ".btnAddResumeItem", function () {
                        ResumeForm.addItem();
                    });
                $(document).off("click", ".resume-item-delete")
                    .on("click", ".resume-item-delete", function () {
                        ResumeForm.removeItem(this);
                    });

                $(document).off('click', '#import-resume')
                        .on('click', '#import-resume', function(){
                    ResumeForm.importResume();
                });

                $(document).off('keypress', $("#resume-item-add-form input,#resume-item-add-form select"))
                    .on('keypress', $("#resume-item-add-form input,#resume-item-add-form select"), function (event) {
                        var keycode = (event.keyCode ? event.keyCode : event.which);
                        var self = event.target;
                        if (keycode == 13) {
                            var allInputs = $("#resume-item-add-form input,#resume-item-add-form select");
                            for (var i = 0; i < allInputs.length; i++) {
                                if (allInputs[i] == self) {
                                    try {
                                        while ((allInputs[i]).name == (allInputs[i + 1]).name) {
                                            i++;
                                        }
                                    } catch (e) {
                                    }
                                    if (allInputs[i].id == "resumeitem-debit" || allInputs[i].id == "resumeitem-credit") {
                                        console.log("ultimo trigger!!!!");
                                        $('.btnAddResumeItem').trigger('click');

                                    } else {
                                        $(allInputs[i + 1]).focus();
                                    }
                                }
                            }
                        }
                });

                $(document).off("change", "#money_box_has_operation_type_id")
                    .on("change", "#money_box_has_operation_type_id", function () {
                    if ($(this).find("option:selected").data("is-debit") == 1) {
                        $("#div_debit").show();
                        $("#div_credit").hide();
                    } else {
                        $("#div_credit").show();
                        $("#div_debit").hide();
                    }
                });

            }

            this.addItem = function () {
                var $form = $("#resume-item-add-form");
                var data = $form.serialize();

                $.ajax({
                    url: $form.attr('action'),
                    data: data,
                    dataType: 'json',
                    type: 'post'
                }).done(function (json) {

                    if (json.status == 'success') {
                        $.pjax.reload({container: '#w_item_form,#w_header_resume'});
                        $("#resumeitem-money_box_has_operation_type_id").focus();
                    } else {

                        //Importante:
                        //https://github.com/yiisoft/yii2/issues/5991 #7260
                        //TODO: actualizar cdo este disponible
                        for (error in json.errors) {
                            $('.field-resumeitem-' + error).addClass('has-error');
                            $('.field-resumeitem-' + error + ' .help-block').text(json.errors[error]);
                        }
                        $("#resumeitem-money_box_has_operation_type_id").focus();
                    }
                });
            }

            this.removeItem = function (element) {
                $elem = $(element);
                var url = $elem.data('url');
                if (confirm($elem.data('confirms'))) {
                    $.ajax({
                        url: url,
                        dataType: 'json',
                        type: 'post'
                    }).done(function (json) {

                        if (json.status == 'success') {
                            $.pjax.reload({container: '#w_header_resume,#w_item_form'});
                        } else {
                        }
                    });
                }

            }

            this.importResume = function() {
                $('#resume_form').submit();
            }
        }
    </script>
<?php $this->registerJs("ResumeForm.init();"); ?>