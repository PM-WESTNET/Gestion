<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 1/12/16
 * Time: 10:09
 */

namespace app\modules\afip\exports\compraVenta;

use app\modules\afip\exports\AbstractLine;

class LineaTipo1Compra extends AbstractLine
{

    /**
     * @param $values
     * @return mixed
     * Campo | Desde | Hasta | Longitud
     * Fecha de comprobante o fecha de oficialización	1	8	8
     * Tipo de comprobante	9	11	3
     * Punto de venta	12	16	5
     * Número de comprobante	17	36	20
     * Despacho de importación	37	52	16
     * Código de documento del vendedor	53	54	2
     * Número de identificación del vendedor	55	74	20
     * Apellido y nombre o denominación del vendedor	75	104	30
     * Importe total de la operación	105	119	15
     * Importe total de conceptos que no integran el precio neto gravado	120	134	15
     * Importe de operaciones exentas	135	149	15
     * Importe de percepciones o pagos a cuenta del Impuesto al Valor Agregado	150	164	15
     * Importe de percepciones o pagos a cuenta de otros impuestos nacionales	165	179	15
     * Importe de percepciones de Ingresos Brutos	180	194	15
     * Importe de percepciones de Impuestos Municipales	195	209	15
     * Importe de Impuestos Internos	210	224	15
     * Código de moneda	225	227	3
     * Tipo de cambio	228	237	10
     * Cantidad de alícuotas de IVA	238	238	1
     * Código de operación	239	239	1
     * Crédito Fiscal Computable	240	254	15
     * Otros Tributos	255	269	15
     * CUIT emisor/corredor	270	280	11
     * Denominación del emisor/corredor	281	310	30
     * IVA comisión	311	325	15
     */
    public function parse($values)
    {
        $include_iva_bill_types = \app\modules\sale\models\BillType::find()->select('code')->where("BINARY name like '% C'")->all();

        $this->values = $values;
        $this->line = (new \DateTime($values['date']))->format('Ymd');
        $this->line .= sprintf("%'.03d", $values['tipo_comprobante']);

        $bill_number = str_replace('-', '', $values['numero_comprobante']);

        $this->line .= sprintf("%'.05d", (((int) substr($bill_number, 0, 4)) == 0) ? 1 : substr($bill_number, 0, 4));
        $this->line .= sprintf("%'.020d", (((int) substr($bill_number, -8)) == 0) ? 1 : substr($bill_number, -8));
        $this->line .= sprintf("%'. 16s", ($values['numero_importacion'] == 0 ? "" : $values['numero_importacion']));
        $this->line .= sprintf("%'.02d", $values['tipo_documento']);
        $this->line .= sprintf("%'.020d", str_replace("-", "", $values['numero_documento']));
        $this->line .= substr(str_pad(iconv('UTF-8', 'ASCII//TRANSLIT', mb_strtoupper($values['empresa'])), 30, " "), 0, 30);
        $this->line .= sprintf("%'.015d", (round(abs($values['total']) * 100, 2)));
        $this->line .= sprintf("%'.015d", (round(abs($values['conceptos_no_incluido_neto']) * 100, 2)));
        $this->line .= sprintf("%'.015d", (round(abs($values['exento']) * 100, 2)));
        $this->line .= sprintf("%'.015d", (round(abs($values['percepciones_a_cuenta_iva']) * 100, 2)));
        $this->line .= sprintf("%'.015d", (round(abs($values['percepciones_a_cuenta_otros']) * 100, 2)));
        $this->line .= sprintf("%'.015d", (round(abs($values['iibb']) * 100, 2)));
        $this->line .= sprintf("%'.015d", (round(abs($values['municipales']) * 100, 2)));
        $this->line .= sprintf("%'.015d", (round(abs($values['internos']) * 100, 2)));
        $this->line .= $values['codigo_moneda'];
        $this->line .= sprintf("%'.010d", (round($values['tipo_de_cambio'] * 1000000, 2)));
        $with_iva = true;
        foreach ($include_iva_bill_types as $bill_type) {
            if ($bill_type->code == $values['tipo_comprobante']) {
                //cantidad de iva 0
                $with_iva = false;
            }
        }
        $this->line .= sprintf("%'.01d", $with_iva ? $values['cantidad_iva'] : 0);
        $this->line .= sprintf("%'.01d", $values['codigo_operacion']);
        $this->line .= sprintf("%'.015d", (round($values['credito_fiscal'] * 100, 2)));
        $this->line .= sprintf("%'.015d", (round($values['otros_tributos'] * 100, 2)));
        $this->line .= sprintf("%'.011d", (round($values['cuit_emisor'] * 100, 2)));
        $this->line .= substr(str_pad(iconv('UTF-8', 'ASCII//TRANSLIT', mb_strtoupper($values['emisor'])), 30, " "), 0, 30);
        $this->line .= sprintf("%'.015d", (round($values['iva_comision'] * 100, 2)));
    }

    /**
     * @return boolean
     */
    public function validate()
    {
        return true;
    }

}