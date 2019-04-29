<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 8/06/18
 * Time: 8:47
 */

namespace app\modules\pagomiscuentas\components\Facturas;


use app\modules\afip\exports\AbstractLine;

/**
 * Class Detalle
 *
 *
 * +-----+-----------------+-------------+---------+---------+------------------------------------------------------------------------+
 * | Nro | Atributo        | Tipode dato | Pos.In. | Pos.Fin | Descripción - Valores Posibles                                         |
 * +-----+-----------------+-------------+---------+---------+------------------------------------------------------------------------+
 * | 1   | Código Registro | N(01)       | 1       | 1       | Código de Registro. Valor fijo: 5.Indica que este renglón              |
 * |     |                 |             |         |         | forma parte del detalle.                                               |
 * +-----+-----------------+-------------+---------+---------+------------------------------------------------------------------------+
 * | 2   | Nro.Referencia  | AN(19)      | 2       | 20      | Identificación del cliente en la empresa. Se refiere a la              |
 * |     |                 |             |         |         | identificación que deberá ingresar el cliente para poder               |
 * |     |                 |             |         |         | pagar, que le sirve a la empresa para saber quien le está              |
 * |     |                 |             |         |         | pagando. Son los números que componen el “concepto utilizado           |
 * |     |                 |             |         |         | como ID del cliente” que la empresa completó en el formulario          |
 * |     |                 |             |         |         | de adhesión.                                                           |
 * +-----+-----------------+-------------+---------+---------+------------------------------------------------------------------------+
 * | 3   | Id.Factura      | AN(20)      | 21      | 40      | Identificación de la factura. Se refiere a la identificación           |
 * |     |                 |             |         |         | particular de la factura que está pagando el cliente. No               |
 * |     |                 |             |         |         | tiene que ser obligatoriamente el “Nro. de Factura”, sino              |
 * |     |                 |             |         |         | que puede ser cualquier número que utilice la empresa para             |
 * |     |                 |             |         |         | individualizar el pago (puede que para un mismo Nro. Referencia,       |
 * |     |                 |             |         |         | haya varios Id. Factura, si un cliente tiene varias facturas a pagar). |
 * +-----+-----------------+-------------+---------+---------+------------------------------------------------------------------------+
 * | 4   | Código Moneda   | N(01)       | 41      | 41      | Código de moneda de los importes informados.Valor fijo: 0 (Pesos).     |
 * |     |                 |             |         |         | Siempre es  0 e indica que la factura es en pesos.                     |
 * +-----+-----------------+-------------+---------+---------+------------------------------------------------------------------------+
 * | 5   | Fecha 1er.Vto.  | N(08)       | 42      | 49      | Fecha del 1er vencimiento de la facturaFormato: AAAAMMDD               |
 * +-----+---------------------+-------------+---------+---------+--------------------------------------------------------------------+
 * | 6   | Importe 1er.Vto.| N(11)       | 50      | 60      | Importe de la factura para el 1er vencimiento.Formato: 9 enteros,      |
 * |     |                 |             |         |         | 2 decimales, sin separadores.                                          |
 * +-----+---------------------+-------------+---------+---------+--------------------------------------------------------------------+
 * | 7   | Fecha 2do.Vto.  | N(08)       | 61      | 68      | Fecha del 2do vencimiento de la facturaFormato: AAAAMMDD               |
 * +-----+---------------------+-------------+---------+---------+--------------------------------------------------------------------+
 * | 8   | Importe 2do.Vto.| N(11)       | 69      | 79      | Importe de la factura para el 2do vencimiento. Formato: 9 enteros,     |
 * |     |                 |             |         |         | 2 decimales, sin separadores.                                          |
 * +-----+---------------------+-------------+---------+---------+--------------------------------------------------------------------+
 * | 9   | Fecha 3er.Vto.  | N(08)       | 80      | 87      | Fecha del 3er vencimiento de la facturaFormato: AAAAMMDD               |
 * +-----+---------------------+-------------+---------+---------+--------------------------------------------------------------------+
 * | 10  | Importe 3er.Vto.| N(11)       | 88      | 98      | Importe de la factura para el 3er vencimiento. Formato: 9 enteros,     |
 * |     |                 |             |         |         | 2 decimales, sin separadores.                                          |
 * +-----+---------------------+-------------+---------+---------+--------------------------------------------------------------------+
 * | 11  | Filler1         | N(19)       | 99      | 117     | Campo para uso futuro. Valor fijo: ceros.                              |
 * +-----+---------------------+-------------+---------+---------+--------------------------------------------------------------------+
 * | 12  | Nro.Referencia  | AN(19)      | 118     | 136     | Se debe repetir la información del campo “Nro. Referencia”.            |
 * |     | Ant.            |             |         |         | En caso que se modifique la identificación del cliente, se             |
 * |     |                 |             |         |         | deberá informar la identificación anterior por única vez,              |
 * |     |                 |             |         |         | luego se deberá repetir la información del campo Nro. Referencia.      |
 * +-----+---------------------+-------------+---------+---------+--------------------------------------------------------------------+
 * | 13  | Mensaje Ticket  | AN(40)      | 137     | 176     | Datos a informar en el ticket de pago. Es el mensaje  que se           |
 * |     |                 |             |         |         | imprimirá en el comprobante de pago que se refiere al concepto         |
 * |     |                 |             |         |         |  abonado por el cliente. Ej: Cuota Noviembre.                          |
 * +-----+-----------------+-------------+---------+---------+------------------------------------------------------------------------+
 * | 14  | Mensaje Pantalla| AN(15)      | 177     | 191     | Datos a informar en la pantalla de selección de la factura a pagar.    |
 * |     |                 |             |         |         | Es el mensaje que verá el cliente en pantalla antes de confirmar el    |
 * |     |                 |             |         |         | pago. Se refiere al mismo concepto que el campo “Mensaje Ticket”,      |
 * |     |                 |             |         |         | pero con menos caracteres.                                             |
 * +-----+------------------+-------------+---------+---------+-----------------------------------------------------------------------+
 * | 15  | Código Barras   | AN(60)      | 192     | 251     | Código de barras.Son los números que componen el código de barras      |
 * |     |                 |             |         |         | de la empresa. Si no posee uno, se debe completar el campo con         |
 * |     |                 |             |         |         | espacios.                                                              |
 * +-----+------------------+-------------+---------+---------+-----------------------------------------------------------------------+
 * | 16  | Filler2         | N(29)       | 252     | 280     | Campo para uso futuro. Valor fijo: ceros.                              |
 * +-----+------------------+-------------+---------+---------+-----------------------------------------------------------------------+
 *
 *
 * @package app\modules\pagomiscuentas\components\Facturas
 */
class Detalle extends AbstractLine
{

    /**
     * @param $values
     * @return mixed
     */
    public function parse($values)
    {
        $this->line = "5";
        $this->line .= str_pad(trim($values['code']), 19, " ");
        $this->line .= str_pad(trim($values['bill_id']), 20, " ");
        $this->line .= "0";

        $this->line .= sprintf("%'.08d", ($values['fecha_1_vto']));
        $this->line .= str_replace(".", "", sprintf("%'.012.2f", $values['importe_1_vto']));

        $this->line .= sprintf("%'.08d", ($values['fecha_2_vto']));
        $this->line .= str_replace(".", "", sprintf("%'.012.2f", $values['importe_2_vto']));

        $this->line .= sprintf("%'.08d", ($values['fecha_3_vto']));
        $this->line .= str_replace(".", "", sprintf("%'.012.2f", $values['importe_3_vto']));

        $this->line .= sprintf("%'.019d", "0");

        $this->line .= str_pad(trim($values['code']), 19, " ");
        $this->line .= substr(str_pad( iconv('UTF-8', 'ASCII//TRANSLIT', mb_strtoupper($values['detalle'])), 40, " "), 0,40);
        $this->line .= substr(str_pad( iconv('UTF-8', 'ASCII//TRANSLIT', mb_strtoupper($values['detalle'])), 15, " "), 0,15);

        $this->line .= substr(str_pad( iconv('UTF-8', 'ASCII//TRANSLIT', mb_strtoupper($values['barcode'])), 60, " "), 0,60);

        $this->line .= sprintf("%'.029d", "0");
    }

    /**
     * @return boolean
     */
    public function validate()
    {
        return true;
    }
}