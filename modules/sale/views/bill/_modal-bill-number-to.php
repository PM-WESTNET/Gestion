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
'header' => '<h2>'.Yii::t('app','Enter bill number to').'</h2>',
'id' => 'bill-number-modal',
'size' => Modal::SIZE_LARGE,
'footer' => Html::a('<span class="glyphicon glyphicon-ok"></span> ' . Yii::t('app', 'Save'), '#', [
                'class' => 'btn btn-success margin-right-quarter',
                'data-toggle' => 'modal',
                'data-target' => '#print-modal',
                'id' => 'btn-set-bill-number'
                ]),

]);
?>
<div class="row">
    <div class="col-sm-6">
        <?= Html::label(Yii::t('app', 'Bill number to'))?> <br>
        <?= Html::input('text', 'ein', '', ['id' => 'input-bill-number-to', 'class' => 'form-control']) ?><br>
    </div>
</div>
<?php
Modal::end();
?>
