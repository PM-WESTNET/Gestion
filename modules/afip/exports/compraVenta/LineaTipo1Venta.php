<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 1/12/16
 * Time: 10:09
 */

namespace app\modules\afip\exports\compraVenta;

use app\modules\afip\exports\AbstractLine;

class LineaTipo1Venta extends AbstractLine
{

    /**
     * @param $values
     * @return mixed
     *
     *  Campo | Desde | Hasta | Longitud
     *  Fecha de comprobante	1	8	8
     *  Tipo de comprobante	9	11	3
     *  Punto de venta	12	16	5
     *  Número de comprobante	17	36	20
     *  Número de comprobante hasta	37	56	20
     *  Código de documento del comprador	57	58	2
     *  Número de identificación del comprador	59	78	20
     *  Apellido y nombre o denominación del comprador	79	108	30
     *  Importe total de la operación	109	123	15
     *  Importe total de conceptos que no integran el precio neto gravado	124	138	15
     *  Percepción a no categorizados	139	153	15
     *  Importe de operaciones exentas	154	168	15
     *  Importe de percepciones o pagos a cuenta de impuestos Nacionales	169	183	15
     *  Importe de percepciones de Ingresos Brutos	184	198	15
     *  Importe de percepciones impuestos Municipales	199	213	15
     *  Importe impuestos internos	214	228	15
     *  Código de moneda	229	231	3
     *  Tipo de cambio	232	241	10
     *  Cantidad de alícuotas de IVA	242	242	1
     *  Código de operación	243	243	1
     *  Otros Tributos	244	258	15
     *  Vecha de vencimiento o pago 259 266 8
     */
    public function parse($values)
    {
        $this->values = $values;

        $this->line = (new \DateTime($values['date']))->format('Ymd');
        $this->line .= sprintf("%'.03d", $values['tipo_comprobante']);

        $bill_number = str_replace('-', '', $values['numero_comprobante']);

        $this->line .= sprintf("%'.05d", $values['punto_de_venta']);
        $this->line .= sprintf("%'.020d", (((int) substr($bill_number, -8)) == 0) ? 1 : substr($bill_number, -8));
        $this->line .= sprintf("%'.020d", $values['numero_comprobante']);
        $this->line .= sprintf("%'.02d", $values['tipo_documento']);
        $this->line .= sprintf("%'.020d", str_replace("-", "", $values['numero_documento']));
        $this->line .= substr(str_pad( iconv('UTF-8', 'ASCII//TRANSLIT', mb_strtoupper($values['empresa'])), 30, " "), 0,30);
        $this->line .= sprintf("%'.015d", (round(abs($values['total'])*100,2)));
        $this->line .= sprintf("%'.015d", (round(abs($values['conceptos_no_incluido_neto'])*100,2)));
        $this->line .= sprintf("%'.015d", (round(abs($values['percepciones_no_categorizadas'])*100,2)));
        $this->line .= sprintf("%'.015d", (round(abs($values['exento'])*100,2)));
        $this->line .= sprintf("%'.015d", (round(abs($values['percepciones_nacionales'])*100,2)));
        $this->line .= sprintf("%'.015d", (round(abs($values['percepciones_iibb'])*100,2)));
        $this->line .= sprintf("%'.015d", (round(abs($values['percepciones_municipales'])*100,2)));
        $this->line .= sprintf("%'.015d", (round(abs($values['impuestos_internos'])*100,2)));
        $this->line .= sprintf("%'.03d", $values['codigo_moneda']);
        $this->line .= sprintf("%'.010d", (round($values['tipo_de_cambio']*1000000,2)));
        $this->line .= sprintf("%'.01d", $values['cantidad_iva']);
        $this->line .= sprintf("%'.01d", $values['codigo_operacion']);
        $this->line .= sprintf("%'.015d", (round($values['otros_tributos']*100,2)));
        $this->line .= ($values['tipo_comprobante']==82 ? '00000000' : (new \DateTime($values['date']))->format('Ymd'));
    }

    /**
     * @return boolean
     */
    public function validate()
    {
        return true;
    }
}