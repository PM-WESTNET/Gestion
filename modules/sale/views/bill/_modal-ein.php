<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 21/02/19
 * Time: 11:50
 */

use yii\bootstrap\Modal;
use yii\helpers\Html;
use kartik\widgets\DatePicker;

$modal = Modal::begin([
'header' => '<h2>'.Yii::t('app','Enter ein and ein expiration').'</h2>',
'id' => 'ein-modal',
'size' => Modal::SIZE_LARGE,
'footer' => Html::a('<span class="glyphicon glyphicon-ok"></span> ' . Yii::t('app', 'Save'), '#', [
                'class' => 'btn btn-success margin-right-quarter',
                'data-toggle' => 'modal',
                'data-target' => '#print-modal',
                'id' => 'btn-set-ein'
                ]),

]);
?>
<div class="row">
    <div class="col-sm-6">
        <?= Html::label(Yii::t('app', 'Ein'))?> <br>
        <?= Html::input('text', 'ein', '', ['id' => 'input-ein', 'class' => 'form-control']) ?><br>
    </div>
    <div class="col-sm-6">
        <?= Html::label(Yii::t('app', 'Ein expiration'))?> <br>
        <?= DatePicker::widget([
            'name' => 'ein-expiration',
            'type' => DatePicker::TYPE_INPUT,
            'pluginOptions' => [
                'autoclose'=>true,
                'format' => 'yyyy-mm-dd'
            ],
            'options' => [
                'id' => 'input-ein-expiration',
                'class' => 'form-control'
            ]
        ]); ?>
    </div>
</div>
<?php
Modal::end();
?>
