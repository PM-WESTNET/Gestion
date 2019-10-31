<?php

use app\components\helpers\ExcelExporter;
use app\modules\afip\models\TaxesBook;
use app\modules\sale\models\TaxRate;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\afip\models\TaxesBook */

class Book {
    /** @var  ExcelExporter $excel */
    public $excel;
    private $isExcel;
    private $data;
    private $model;
    private $totals = [];
    private $taxes = [];
    private $pageTotals = [];
    private $columns = [];

    public function __construct($model, $data, $isExcel)
    {
        $this->isExcel = $isExcel;
        $this->model = $model;
        $this->data = $data;
        $this->taxes = TaxRate::find()->all();
        foreach($this->taxes as $tax) {
            $this->taxNames[$tax->tax_id] =  $tax->tax->name;
        }
    }

    private function printHeader($page)
    {
        $this->pageTotals = [
            'net' => 0,
            'total' => 0
        ];

        if($this->isExcel) {
            $cols = [];
            foreach ($this->columns as $column) {
                $cols[] = $column[1];
            }

            $this->excel->writeRow(['', '', '',
                Yii::t('afip', 'Book ' . ucfirst($this->model->type))
            ], 0, false);

            $this->excel->writeRow([
                Yii::t('afip', 'Business Name') . ": " . $this->model->company->name, '', '', '',
                Yii::t('afip', 'Register Number') . ": " . $this->model->number, '', '',
            ], 0, false);
            $this->excel->writeRow([
                Yii::t('app', 'Tax Identification') . ": " . $this->model->company->tax_identification
            ], 0, false);
            $this->excel->writeRow([
                Yii::t('app', 'Address') . ": " . $this->model->company->address,
                Yii::t('afip', 'Period') . ": " . Yii::$app->getFormatter()->asDate($this->model->period, 'M/yyyy'), '', '',
                Yii::t('afip', 'Page') . ": " . $page
            ], 0, false);

            $this->excel->writeRow($cols, 0, false);
        } else { ?>
            <table width="100%" class="header">
                <tbody>
                <tr>
                    <td colspan="3"
                        style="text-align: center"><?= Yii::t('afip', 'Book ' . ucfirst($this->model->type)) ?></td>
                </tr>
                <tr>
                    <td><?= Yii::t('afip', 'Business Name') . ": " . $this->model->company->name ?> </td>
                    <td>&nbsp;</td>
                    <td style="text-align: right"><?= Yii::t('afip', 'Register Number') . ": " . $this->model->number ?> </td>
                </tr>
                <tr>
                    <td colspan="2"><?= Yii::t('app', 'Tax Identification') . ": " . $this->model->company->tax_identification ?> </td>
                </tr>
                <tr>
                    <td><?= Yii::t('app', 'Address') . ": " . $this->model->company->address ?> </td>
                    <td><?= Yii::t('afip', 'Period') . ": " . Yii::$app->getFormatter()->asDate($this->model->period, 'M/yyyy') ?> </td>
                    <td style="text-align: right"><?= Yii::t('afip', 'Page') . ": " . $page ?> </td>
                </tr>
                </tbody>
            </table>
            <table width="100%" class="data">
            <thead>
            <tr>
                <th class="date"><?= Yii::t('app', 'Date') ?></th>
                <th class="bussines_name"><?= Yii::t('app', 'Business Name') ?></th>
                <th class="tax_identification"><?= Yii::t('app', 'Tax Identification') ?></th>
                <th class="bill"><?= Yii::t('app', 'Bill') ?></th>
                <th class="amount"><?= Yii::t('app', 'Net amount') ?></th>
                <?php foreach (TaxRate::find()->all() as $tax) {
                $tax_name = $tax->tax->name . ' ' .($tax->pct*100) . '%';
                if($tax_name != 'IVA 0%' && $tax_name != 'IVA 5%' && $tax_name != 'IVA 2.5%'){?>
                    <th class="amount"><?= Yii::t('app', $tax->tax->name . ' ' . ($tax->pct * 100) . '%') ?></th>
                <?php } }?>
                <th class="amount"><?= Yii::t('app', 'Total') ?></th>
            </tr>
            </thead>
            <?php
        }
    }

    private function printRow($value)
    {
        if ($this->model->type == 'buy') {
            $this->pageTotals['net'] += $value['neto'];
            $this->pageTotals['total'] += $value['total'];
        } else {
            $this->pageTotals['net'] += $value['net'];
            $this->pageTotals['total'] += $value['total'];
        }

        if ($this->isExcel) {
            if ($this->model->type == 'buy') {
                $data = [
                    'date' => Yii::$app->getFormatter()->asDate($value['date']),
                    'business_name' => $value['empresa'],
                    'tax_identification' => $value['numero_documento'],
                    'number' => $value['nombre_tipo_comprobante'] . " - " . $value['numero_comprobante'],
                    'net' => $value['neto']
                ];
            } else {
                $data = [
                    'date' => Yii::$app->getFormatter()->asDate($value['date']),
                    'business_name' => $value['business_name'],
                    'tax_identification' => $value['tax_identification'],
                    'number' => $value['bill_type'] . " - " . $value['number'],
                    'net' => $value['net']
                ];
            }

            foreach ($this->taxes as $tax) {
                $tax_name = $tax->tax->name . ' ' . ($tax->pct * 100) . '%';
                if ($tax_name != 'IVA 0%' && $tax_name != 'IVA 5%' && $tax_name != 'IVA 2.5%') {

                    if ($this->model->type == 'buy') {

                        if (($tax->pct * 100) == 21) {
                            $data[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] = $value['iva_21'];
                            $this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] += $value['iva_21'];
                        }

                        if (($tax->pct * 100) == 10.5) {
                            $data[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] = $value['iva_105'];
                            $this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] += $value['iva_105'];
                        }

                        if (($tax->pct * 100) == 27) {
                            $data[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] = $value['iva_27'];
                            $this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] += $value['iva_27'];
                        }

                        //Para evitar que la consulta se realice mas de una vez por tax.
                        $tax_slug = $tax->tax->slug;

                        if ($tax_slug == 'ingresos-brutos') {

                            $data[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] = $value['iibb'];
                            $this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] += $value['iibb'];
                        }

                        if ($tax_slug == 'cptos-no-grav') {
                            $data[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] = $value['conceptos_no_incluido_neto'];
                            $this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] += $value['conceptos_no_incluido_neto'];
                        }

                        if ($tax_slug == 'percep-iva') {
                            $data[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] = $value['percepciones_a_cuenta_iva'];
                            $this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] += $value['percepciones_a_cuenta_iva'];
                        }

                        if ($tax_slug == 'percep-ing-b') {
                            $data[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] = $value['municipales'];
                            $this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] += $value['municipales'];
                        }

                        if ($tax_slug == 'retenc-iva') {
                            $data[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] = $value['retencion_iva'];
                            $this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] += $value['retencion_iva'];
                        }

                        if ($tax_slug == 'retenc-ing-b') {
                            $data[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] = $value['retencion_ingresos_brutos'];
                            $this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] += $value['retencion_ingresos_brutos'];
                        }

                        if ($tax_slug == 'retenc-gan') {
                            $data[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] = $value['internos'];
                            $this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] += $value['internos'];
                        }

                        if ($tax_slug == 'iva-otros') {
                            $data[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] = $value['percepciones_a_cuenta_otros'];
                            $this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] += $value['percepciones_a_cuenta_otros'];
                        }

                    } else {
                        $this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] += $value[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'];
                        $data[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] = $value[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'];
                    }
                }
            }
            $data['total'] = $value['total'];

            $this->excel->writeRow($data, 0);

        } else {
            echo "<tr>";

            if ($this->model->type == 'buy') { ?>
                <td class="date"><?= Yii::$app->getFormatter()->asDate($value['date']) ?></td>
                <td class="bussines_name"><?= $value['empresa'] ?></td>
                <td class="tax_identification"><?= $value['numero_documento'] ?></td>
                <td class="bill"><?= $value['nombre_tipo_comprobante'] . " - " . $value['numero_comprobante'] ?></td>
                <td class="amount"><?= Yii::$app->getFormatter()->asCurrency($value['neto']) ?></td>


                <?php foreach ($this->taxes as $tax) {
                    $tax_name = $tax->tax->name . ' ' . ($tax->pct * 100) . '%';
                    if ($tax_name != 'IVA 0%' && $tax_name != 'IVA 5%' && $tax_name != 'IVA 2.5%') {

                        if ($this->model->type == 'buy') {

                            if (($tax->pct * 100) == 21) {
                                $data[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] = $value['iva_21'];
                                $this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] += $value['iva_21'];
                            }

                            if (($tax->pct * 100) == 10.5) {
                                $data[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] = $value['iva_105'];
                                $this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] += $value['iva_105'];
                            }

                            if (($tax->pct * 100) == 27) {
                                $data[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] = $value['iva_27'];
                                $this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] += $value['iva_27'];
                            }

                            //Para evitar que la consulta se realice mas de una vez por tax.
                            $tax_slug = $tax->tax->slug;

                            if ($tax_slug == 'ingresos-brutos') {

                                $data[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] = $value['iibb'];
                                $this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] += $value['iibb'];
                            }

                            if ($tax_slug == 'cptos-no-grav') {
                                $data[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] = $value['conceptos_no_incluido_neto'];
                                $this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] += $value['conceptos_no_incluido_neto'];
                            }

                            if ($tax_slug == 'percep-iva') {
                                $data[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] = $value['percepciones_a_cuenta_iva'];
                                $this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] += $value['percepciones_a_cuenta_iva'];
                            }

                            if ($tax_slug == 'percep-ing-b') {
                                $data[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] = $value['municipales'];
                                $this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] += $value['municipales'];
                            }

                            if ($tax_slug == 'retenc-iva') {
                                $data[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] = $value['retencion_iva'];
                                $this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] += $value['retencion_iva'];
                            }

                            if ($tax_slug == 'retenc-ing-b') {
                                $data[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] = $value['retencion_ingresos_brutos'];
                                $this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] += $value['retencion_ingresos_brutos'];
                            }

                            if ($tax_slug == 'retenc-gan') {
                                $data[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] = $value['internos'];
                                $this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] += $value['internos'];
                            }

                            if ($tax_slug == 'iva-otros') {
                                $data[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] = $value['percepciones_a_cuenta_otros'];
                                $this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] += $value['percepciones_a_cuenta_otros'];
                            }


                        } else {
                            $this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] += $value[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'];
                            $data[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] = $value[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'];
                        }

                        echo "<td class='amount'>" . Yii::$app->getFormatter()->asCurrency($data[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%']) . "</td>";
                    }
                }

                echo " <td class='amount'>" . Yii::$app->getFormatter()->asCurrency($value['total']) . "</td></tr>";

            } else { ?>
                <td class="date"><?= Yii::$app->getFormatter()->asDate($value['date']) ?></td>
                <td class="bussines_name"><?= $value['business_name'] ?></td>
                <td class="tax_identification"><?= $value['tax_identification'] ?></td>
                <td class="bill"><?= $value['bill_type'] . " - " . $value['number'] ?></td>
                <td class="amount"><?= Yii::$app->getFormatter()->asCurrency($value['net']) ?></td>

                <?php foreach ($this->taxes as $tax) {
                    $tax_name = $tax->tax->name . ' ' . ($tax->pct * 100) . '%';
                    if ($tax_name != 'IVA 0%' && $tax_name != 'IVA 5%' && $tax_name != 'IVA 2.5%') {
                            $this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'] += $value[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'];
                            echo "<td class='amount'>". Yii::$app->getFormatter()->asCurrency($value[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%']) ."</td>";
                     }
                }
                echo " <td class='amount'>" . Yii::$app->getFormatter()->asCurrency($value['total']) . "</td></tr>";
            }
        }
    }

    private function sumTotals() {
        if(count($this->totals)==0) {
            $this->totals = array_fill_keys(array_keys($this->pageTotals), 0);
        }

        // Sumo a los totales del reporte
        foreach($this->pageTotals as $key => $value) {
            $this->totals[$key] += $value;
        }
    }

    private function printTotal()
    {
        if($this->isExcel) {
            $data = [
                Yii::t('afip', 'Total of Period'),
                '',
                '',
                '',
                $this->totals['net']
            ];
            foreach ($this->taxes as $tax) {
                $tax_name = $tax->tax->name . ' ' .($tax->pct*100) . '%';
                if($tax_name != 'IVA 0%' && $tax_name != 'IVA 5%' && $tax_name != 'IVA 2.5%'){
                    $data[] = $this->totals[$tax->tax->name . ' ' . ($tax->pct * 100) . '%'];
                }
            }

            $data[] = $this->totals['total'];

            $this->excel->writeRow($data, 0, false);

        } else {
            ?>
            <tr>
                <td colspan="4" style="text-align: right"><?= Yii::t('afip', 'Total of Period') ?></td>
                <td class="amount"><?= Yii::$app->getFormatter()->asCurrency($this->totals['net']) ?></td>
                <?php foreach (TaxRate::find()->all() as $tax) {
                    $tax_name = $tax->tax->name . ' ' .($tax->pct*100) . '%';
                    if($tax_name != 'IVA 0%' && $tax_name != 'IVA 5%' && $tax_name != 'IVA 2.5%'){?>
                        <td class="amount"><?= Yii::$app->getFormatter()->asCurrency($this->totals[$tax->tax->name . ' ' . ($tax->pct * 100) . '%']) ?></td>
                <?php }  }?>
                <td class="amount"><?= Yii::$app->getFormatter()->asCurrency($this->totals['total']) ?></td>
            </tr>
            <?php
        }
    }

    public function printReport()
    {
        if($this->isExcel) {
            $this->excel = ExcelExporter::getInstance();
            $this->columns = [
                'A' => ['date', Yii::t('app', 'Date' ), PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY],
                'B' => ['business_name', Yii::t('app', 'Business Name' ), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
                'C' => ['tax_identification', Yii::t('app', 'Tax Identification' ), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
                'D' => ['number', Yii::t('app', 'Bill' ), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
                'E' => ['net', Yii::t('app', 'Net amount' ), PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00],
            ];
            $col = 'F';
            foreach(TaxRate::find()->all() as $tax) {
                $tax_name = $tax->tax->name . ' ' .($tax->pct*100) . '%';
                if($tax_name != 'IVA 0%' && $tax_name != 'IVA 5%' && $tax_name != 'IVA 2.5%') {
                    $this->columns[$col] = [Yii::t('app', $tax->tax->name . ' ' . ($tax->pct * 100) . '%'), Yii::t('app', $tax->tax->name . ' ' . ($tax->pct * 100) . '%'), PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00];
                    $col++;
                }
            }
            $this->columns[$col] = ['total', Yii::t('app', 'Total'), PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00 ];
            $this->excel->create('IVA-'.($this->model->type == 'buy' ? 'Compras': 'Ventas' ), $this->columns);
        }
        $models = $this->data->getModels();
        $page = null;
        foreach($models as $model){
            if ($page != $model['page']) {
                if($page) {
                    //$this->sumTotals();
                    $this->printPageFoot();
                }
                $this->printHeader($model['page']);

                foreach($this->taxes as $tax) {
                    $tax_name = ($tax->pct*100) . '%';
                    if($tax_name != '0%' || $tax_name != '5%' || $tax_name != '2.5%'){
                        $this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . $tax_name] = 0;
                    }
                }
                if(!$this->isExcel) {
                    echo '<tbody>';
                }
            }
            $this->printRow($model);
            $page = $model['page'];
        }
        $this->printPageFoot(true);

    }

    public function printPageFoot($lastPage=false){
        if($this->isExcel) {
            $data = [
                '',
                '',
                '',
                '',
                $this->pageTotals['net']
            ];
            foreach ($this->taxes as $tax) {
                $data[] = $this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%'];
            }
            $data[] = $this->pageTotals['total'];

            $this->excel->writeRow($data, 0, false);

            $this->sumTotals($this->pageTotals);
            if ($lastPage) {
                $this->printTotal();
            }
            $this->excel->setRow($this->excel->getRow()+2);
        } else {
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="4">&nbsp;</td>
                <td class="amount"><?= Yii::$app->getFormatter()->asCurrency($this->pageTotals['net']) ?></td>
                <?php foreach (TaxRate::find()->all() as $tax) {
                    $tax_name = $tax->tax->name . ' ' .($tax->pct*100) . '%';
                    if($tax_name != 'IVA 0%' && $tax_name != 'IVA 5%' && $tax_name != 'IVA 2.5%'){


                    ?>
                    <td class="amount"><?= Yii::$app->getFormatter()->asCurrency($this->pageTotals[$this->taxNames[$tax->tax_id] . ' ' . ($tax->pct * 100) . '%']) ?></td>
                <?php } }?>
                <td class="amount"><?= Yii::$app->getFormatter()->asCurrency($this->pageTotals['total']) ?></td>
            </tr>
            <?php
            $this->sumTotals($this->pageTotals);
            if ($lastPage) {
                $this->printTotal();
            } ?>
            </tfoot>
            </table>
            <?php if (!$lastPage) { ?>
                <div style="page-break-after: always"></div>
            <?php }
        }
    }
}
if(!$excel) {
    ?>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: "Tahoma", "sans-serif";
            font-size: 12px;
        }

        table {
            font-family: "Tahoma", "sans-serif";
            font-size: 9px;
        /*/ / border: 1 px solid black;*/
        }

        table.header {
            font-family: "Tahoma", "sans-serif";
            font-size: 12px;
            margin-bottom: 5px;
            margin-top: 5px;
        }

        table.data th /*, table.data td */
        {
            border: 1px solid black;
        }

        table.data th.date, table.data td.date {
            width: 130px;
            text-align: center;
        }

        table.data th.bussines_name, table.data td.bussines_name {
            /*white-space:nowrap;*/
            width: 400px;
        }

        table.data th.tax_identification, table.data td.tax_identification {
            width: 110px;
            text-align: center;
        }

        table.data th.bill, table.data td.bill {
            width: 180px;
        }

        table.data th.amount {
            width: 100px;
            text-align: center;
        }

        table.data td.amount {
            text-align: right;
            padding-right: 2px;
        }

        table.data thead tr {
            border-bottom: 1px solid black;
        }

        table.data tfoot td {
            border-top: 1px solid black;
        }
    </style>
    <?php
}
try {
    $book = new Book($model, $dataProvider, $excel);
    $book->printReport();
    if($excel) {
        $book->excel->download('IVA-'.($model->type == 'buy' ? 'Compras': 'Ventas').'.xls', false);
    }
} catch(\Exception $ex) {
    error_log($ex->getLine() . " - " .  $ex->getMessage());
    error_log($ex->getTraceAsString());
}
?>