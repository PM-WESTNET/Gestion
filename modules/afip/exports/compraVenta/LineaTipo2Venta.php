<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 1/12/16
 * Time: 10:09
 */

namespace app\modules\afip\exports\compraVenta;


use app\modules\afip\exports\AbstractLine;

class LineaTipo2Venta extends AbstractLine
{
    /**
     * @param $values
     * @return mixed
     * Campo | Desde | Hasta | Longitud
     * Tipo de comprobante	1	3	3
     * Punto de venta	4	8	5
     * Número de comprobante	9	28	20
     * Importe neto gravado	29	43	15
     * Alícuota de IVA	44	47	4
     * Impuesto Liquidado	48	62	15
     */
    public function parse($values)
    {
        $this->values = $values;
        $this->line = sprintf("%'.03d", $values['tipo_comprobante']);
        $this->line .= sprintf("%'.05d", $values['punto_de_venta']);
        $this->line .= sprintf("%'.020d", $values['numero_comprobante']);
        $this->line .= sprintf("%'.015d", (round(abs($values['neto'])*100,2)));
        $this->line .= sprintf("%'.04d", ($values['tipo_de_iva'] ? $values['tipo_de_iva'] : 5 ));
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