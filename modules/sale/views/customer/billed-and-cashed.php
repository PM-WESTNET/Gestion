<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;
use kartik\export\ExportMenu;
use yii\helpers\ArrayHelper;
use app\modules\sale\models\CustomerClass;
use app\modules\westnet\models\Node;
use kartik\widgets\Select2;
use app\modules\sale\models\Company;
use yii\bootstrap\Collapse;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\sale\models\search\CustomerSearch $searchModel
 */

$this->title = Yii::t('app', 'Billed and Cashed');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>        
    </div>


    <div class="search">
        <div class="container">
            <?php $form = ActiveForm::begin(['method'=>'get']); ?>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($searchModel, 'fromDate')->widget(DatePicker::className(), [
                        'language' => Yii::$app->language,
                        'dateFormat' => 'dd-MM-yyyy',
                        'options'=>[
                            'class'=>'form-control dates',

                        ]
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($searchModel, 'toDate')->widget(DatePicker::className(), [
                        'language' => Yii::$app->language,
                        'dateFormat' => 'dd-MM-yyyy',
                        'options'=>[
                            'class'=>'form-control dates',

                        ]
                    ]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <?= Html::submitButton( Yii::t('app', 'Search'), ['class' => 'btn btn-success', 'id'=>'submitButton']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo Yii::t('app', 'Billed') ?></div>
                        <div class="panel-body">
                            <table class="kv-grid-table table table-bordered table-striped kv-table-wrap" >
                                <thead>
                                <tr>
                                    <th><?= Yii::t('app', 'Company')?></th>
                                    <th><?= Yii::t('app', 'Net Billed')?></th>
                                    <th><?= Yii::t('app', 'Net Credit Note')?></th>
                                    <th><?= Yii::t('app', 'Net Total')?></th>
                                    <th><?= Yii::t('app', 'Taxes')?></th>
                                    <th><?= Yii::t('app', 'Total')?></th>
                                </tr>
                                </thead>
                                <tbody id="content">

                                <?php
                                $totales = ['FC'=>0, 'NC'=>0, 'taxes'=>0];
                                foreach ($billed as $key => $compTotal){
                                    echo '<tr id="f'.$key.'">';
                                    echo '<td>' . $compTotal['name'] . '</td>';
                                    echo '<td class="text-right">' . Yii::$app->formatter->asCurrency($compTotal['FC']) . '</td>';
                                    echo '<td class="text-right">' . Yii::$app->formatter->asCurrency(abs($compTotal['NC'])) . '</td>';
                                    echo '<td class="text-right">' . Yii::$app->formatter->asCurrency($compTotal['FC'] + $compTotal['NC']) . '</td>';
                                    echo '<td class="text-right">' . Yii::$app->formatter->asCurrency($compTotal['taxes']) . '</td>';
                                    echo '<td class="text-right">' . Yii::$app->formatter->asCurrency($compTotal['FC'] + $compTotal['NC'] + $compTotal['taxes']) . '</td>';
                                    echo '</tr>';
                                    $totales['FC'] += $compTotal['FC'];
                                    $totales['NC'] += abs($compTotal['NC']);
                                    $totales['taxes'] += $compTotal['taxes'];
                                }
                                ?>
                                </tbody>
                                <tfoot>
                                <?php
                                    echo '<tr id="f'.$key.'">';
                                    echo '<td class="" style="font-weight: bold">' . Yii::t('app', 'Total') . '</td>';
                                    echo '<td class="text-right" style="font-weight: bold">' . Yii::$app->formatter->asCurrency($totales['FC']) . '</td>';
                                    echo '<td class="text-right" style="font-weight: bold">' . Yii::$app->formatter->asCurrency(abs($totales['NC'])) . '</td>';
                                    echo '<td class="text-right" style="font-weight: bold">' . Yii::$app->formatter->asCurrency($totales['FC'] - $totales['NC']) . '</td>';
                                    echo '<td class="text-right" style="font-weight: bold">' . Yii::$app->formatter->asCurrency($totales['taxes']) . '</td>';
                                    echo '<td class="text-right" style="font-weight: bold">' . Yii::$app->formatter->asCurrency(($totales['FC'] - abs($totales['NC'])) + $totales['taxes']) . '</td>';
                                    echo '</tr>';
                                ?>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="panel panel-success">
                        <div class="panel-heading"><?php echo Yii::t('app', 'Cashed') ?></div>
                        <div class="panel-body">
                            <?php
                                $aCashed = [];
                                $methods = [];
                                $totales = [];
                                $company = "";
                                foreach ($cashed as $cash){
                                    $aCashed[$cash['name']][$cash['payment_method']] = $cash['total'];
                                    if(array_search($cash['payment_method'], $methods) === false){
                                        $methods[] = $cash['payment_method'];
                                    }
                                }
                            ?>
                            <table class="kv-grid-table table table-bordered table-striped kv-table-wrap" >
                                <thead>
                                <tr>
                                    <th><?= Yii::t('app', 'Company')?></th>
                                    <?php
                                        foreach ($methods as $method) {
                                            echo "<th>". $method ."</th>";
                                            $totales[$method] = 0;
                                        }
                                    ?>
                                    <th><?= Yii::t('app', 'Total')?></th>
                                </tr>
                                </thead>
                                <tbody id="content">

                                <?php
                                    foreach($aCashed as $key => $item) {
                                        echo "<tr>";
                                        echo "<th class='text-left'>". $key ."</th>";
                                        $subtotal = 0;
                                        foreach ($methods as $method) {
                                            echo '<td class="text-right">' . Yii::$app->formatter->asCurrency((array_key_exists($method, $item)!==false?$item[$method]:0)) . '</td>';
                                            $subtotal += (array_key_exists($method, $item)!==false?$item[$method]:0);
                                            $totales[$method] +=  (array_key_exists($method, $item)!==false?$item[$method]:0);
                                        }
                                        echo '<td class="text-right">' . Yii::$app->formatter->asCurrency($subtotal) . '</td>';
                                        echo "</tr>";

                                    }
                                ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                    <?php
                                        $total = 0;
                                        echo '<td class="" style="font-weight: bold">' . Yii::t('app', 'Total') . '</td>';
                                        foreach ($methods as $method) {
                                            echo '<td class="text-right" style="font-weight: bold">' . Yii::$app->formatter->asCurrency($totales[$method]) . '</td>';
                                            $total += $totales[$method];
                                        }
                                        echo '<td class="text-right" style="font-weight: bold">' . Yii::$app->formatter->asCurrency($total) . '</td>';
                                    ?>

                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
