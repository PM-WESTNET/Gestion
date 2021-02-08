<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 1/12/16
 * Time: 10:09
 */

namespace app\modules\afip\exports\compraVenta;


use app\modules\afip\exports\AbstractLine;

class LineaTipo2Compra extends AbstractLine
{
    /**
     * @param $values
     * @return mixed
     * Campo | Desde | Hasta | Longitud
     * Tipo de comprobante	1	3	3
     * Punto de venta	4	8	5
     * Número de comprobante	9	28	20
     * Código de documento del vendedor	29	30	2
     * Número de identificación del vendedor	31	50	20
     * Importe neto gravado	51	65	15
     * Alícuota de IVA	66	69	4
     * Impuesto liquidado	70	84	15
     */
    public function parse($values)
    {
        $this->values = $values;
        $bill_number = str_replace('-', '', $values['numero_comprobante']);

        $this->line = sprintf("%'.03d", $values['tipo_comprobante']);
        $this->line .= sprintf("%'.05d", (((int) substr($bill_number, 0, 4)) == 0) ? 1 : substr($bill_number, 0, 4));
        $this->line .= sprintf("%'.020d", (((int) substr($bill_number, -8)) == 0) ? 1 : substr($bill_number, -8));
        $this->line .= sprintf("%'.02d", $values['tipo_documento']);
        $this->line .= sprintf("%'.020d", str_replace('-', '', $values['numero_documento']));
        $this->line .= sprintf("%'.015d", (round(abs($values['neto'])*100,2)));
        $this->line .= sprintf("%'.04d", $values['tipo_de_iva']);
        $this->line .= sprintf("%'.015d", (round(abs($values['impuesto_liquidado'])*100,2)));
    }

    /**
     * @return boolean
     */
    public function validate()
    {
        return true;
    }
}