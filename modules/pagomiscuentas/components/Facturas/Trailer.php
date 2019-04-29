<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 8/06/18
 * Time: 8:48
 */

namespace app\modules\pagomiscuentas\components\Facturas;


use app\modules\afip\exports\AbstractLine;

/**
 * Class Trailer
 *
 * +-----+-----------------+-------------+---------+---------+-----------------------------------------------------------------+
 * | Nro | Atributo        | Tipode dato | Pos.In. | Pos.Fin | Descripción – Valores Posibles                                  |
 * +-----+-----------------+-------------+---------+---------+-----------------------------------------------------------------+
 * | 1   | Codigo Registro | N(01)       | 1       | 1       | Código de Registro. Valor fijo: 9.Siempre es  9 e indica que    |
 * |     |                 |             |         |         | este renglón es el trailer.                                     |
 * +-----+-----------------+-------------+---------+---------+-----------------------------------------------------------------+
 * | 2   | Código Prisma   | N(03)       | 2       | 4       | Identificador Prisma. Valor fijo: 400. Siempre es 400 e indica  |
 * |     |                 |             |         |         | que el ente recaudador es Prisma.                               |
 * +-----+-----------------+-------------+---------+---------+-----------------------------------------------------------------+
 * | 3   | Codigo Empresa  | N(04)       | 5       | 8       | Nro. de empresa asignado por Prisma. Son los cuatro dígitos     |
 * |     |                 |             |         |         | que figuran en el mail de “Confirmación de aprobación de        |
 * |     |                 |             |         |         | solicitud” que recibe la empresa.                               |
 * +-----+-----------------+-------------+---------+---------+-----------------------------------------------------------------+
 * | 4   | Fecha Archivo   | N(08)       | 9       | 16      | Fecha de generación del archivo.Formato: AAAAMMDD               |
 * +-----+-----------------+-------------+---------+---------+-----------------------------------------------------------------+
 * | 5   | Cant.Registros  | N(07)       | 17      | 23      | Cantidad de registros de detalle informados.Es la cantidad de   |
 * |     |                 |             |         |         | renglones que tiene el detalle.                                 |
 * +-----+-----------------+-------------+---------+---------+-----------------------------------------------------------------+
 * | 6   | Filler1         | N(07)       | 24      | 30      | Campo para uso futuro. Valor fijo: ceros.                       |
 * +-----+-----------------+-------------+---------+---------+-----------------------------------------------------------------+
 * | 7   | Total Importe   | N(16)       | 31      | 46      | Sumatoria del campo ‘Importe 1er.Vto.’ de los registros de      |
 * |     |                 |             |         |         | detalle. Formato: 14 enteros, 2 decimales, sin separadores.     |
 * +-----+-----------------+-------------+---------+---------+-----------------------------------------------------------------+
 * | 8   | Filler2         | N(234)      | 47      | 280     | Campo para uso futuro. Valor fijo: ceros.                       |
 * +-----+-----------------+-------------+---------+---------+-----------------------------------------------------------------+
 * @package app\modules\pagomiscuentas\components\Facturas
 */
class Trailer extends AbstractLine
{

    /**
     * @param $values
     * @return mixed
     */
    public function parse($values)
    {
        $this->line = "9400";
        $this->line .= sprintf("%'.04d", ($values['company']));
        $this->line .= sprintf("%'.08d", ($values['date']));
        $this->line .= sprintf("%'.07d", ($values['cantidad']));
        $this->line .= sprintf("%'.07d", "0");
        $this->line .= str_replace(".", "", sprintf("%'.017.2f", $values['total']));
        $this->line .= sprintf("%'.0234d", "0");
    }

    /**
     * @return boolean
     */
    public function validate()
    {
        return true;
    }
}