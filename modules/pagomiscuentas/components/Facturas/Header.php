<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 8/06/18
 * Time: 7:59
 */

namespace app\modules\pagomiscuentas\components\Facturas;


use app\modules\afip\exports\AbstractLine;

/**
 * Class Header
 * Estructura del Header.
 *
 * +-----+-----------------+-------------+---------+---------+------------------------------------------------------------------+
 * | Nro | Atributo        | Tipode dato | Pos.In. | Pos.Fin | Descripción - Valores Posibles                                   |
 * +-----+-----------------+-------------+---------+---------+------------------------------------------------------------------+
 * | 1   | Código Registro | N(01)       | 1       | 1       | Código de Registro. Valor fijo: 0. Siempre es 0 e indica que     |
 * |     |                 |             |         |         | este renglón es el header.                                       |
 * +-----+-----------------+-------------+---------+---------+------------------------------------------------------------------+
 * | 2   | Código Prisma   | N(03)       | 2       | 4       | Identificador Prisma. Valor fijo: 400. Siempre es 400 e indica   |
 * |     |                 |             |         |         | que el ente recaudador es Prisma.                                |
 * +-----+-----------------+-------------+---------+---------+------------------------------------------------------------------+
 * | 3   | Codigo Empresa  | N(04)       | 5       | 8       | Nro. de empresa asignado por Prisma. Son los cuatro dígitos que  |
 * |     |                 |             |         |         | figuran en el mail de “Confirmación de aprobación de solicitud”  |
 * |     |                 |             |         |         | que recibe la empresa.                                           |
 * +-----+-----------------+-------------+---------+---------+------------------------------------------------------------------+
 * | 4   | Fecha Archivo   | N(08)       | 9       | 16      | Fecha de generación del archivo.Formato: AAAAMMDD                |
 * +-----+-----------------+-------------+---------+---------+------------------------------------------------------------------+
 * | 5   | Filler          | N(264)      | 17      | 280     | Campo para uso futuro. Valor fijo: ceros.                        |
 * +-----+-----------------+-------------+---------+---------+------------------------------------------------------------------+
 *
 * @package app\modules\pagomiscuentas\components\Facturas
 */
class Header extends AbstractLine
{

    /**
     * @param $values
     * @return mixed
     */
    public function parse($values)
    {
        $this->line = "0400";
        $this->line .= sprintf("%'.04d", ($values['company']));
        $this->line .= sprintf("%'.08d", ($values['date']));
        $this->line .= sprintf("%'.0264d", "0");
    }

    /**
     * @return boolean
     */
    public function validate()
    {
        return true;
    }
}