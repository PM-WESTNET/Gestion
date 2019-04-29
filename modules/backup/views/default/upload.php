<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
//use kartik\widgets\FileInput;
use kartik\file\FileInput;


/* @var $this yii\web\View */
/* @var $model app\models\Document */
/* @var $form yii\widgets\ActiveForm */


$this->params['breadcrumbs'][]= [
'label'	=> Yii::t('backup','Upload'),
'url'	=> array('upload'),
];?>

<h1><?= Yii::t('backup','Upload') ?></h1>


<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>


<?= $form->field($model, 'upload_file')->widget(FileInput::classname(), [
    'options' => ['accept' => 'mysql/*.sql'],
]); ?>


<div class="form-group">
    <?=
    Html::submitButton( Yii::t('backup','Save') ,
        ['class' => 'btn btn-success']
    ) ?>
</div>

<?php ActiveForm::end(); ?>

<!-- form -->
