<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 8/06/18
 * Time: 16:01
 */

namespace app\modules\pagomiscuentas\components\Cobranza;


use app\modules\pagomiscuentas\models\PagomiscuentasFile;
use Yii;

/**
 * Class CobranzaReader
 *
 *
 * @package app\modules\pagomiscuentas\components\Cobranza
 */
class CobranzaReader
{
    public function parse(PagomiscuentasFile $pagomiscuentasFile)
    {
        $file = null;
        $datas = [];
        try {
            $file = fopen(Yii::getAlias('@webroot') ."/".$pagomiscuentasFile->path, 'r');
            $i = 0;
            while ($line = fgets($file)) {
                if($i!=0) {
                    $canal = substr($line, 77, 2);
                    $data = [
                        'customer_id'   => trim(substr($line, 1, 19)),
                        'bill_id'       => trim(substr($line, 20, 20)),
                        'fecha_cobro'   => trim(substr($line, 49, 8)),
                        'importe'       => trim(substr($line, 57, 11)),
                        'canal'         => ($canal=='PC' ? 'Pago mis cuentas' : ($canal=='HB' ? 'Home Banking' : 'Desconocido' ) )
                    ];
                    $datas[] = $data;
                }
                $i++;
            }
            $datas = array_slice($datas, 0, count($datas)-1);
        } catch (\Exception $ex){
            error_log($ex->getMessage());
        }
        if($file) {
            fclose($file);
        }
        return $datas;
    }
}

/*
 *  HEADER
 *  +-----+------------------+-------------+---------+---------+-------------------------------------------------------------------+
 *  | Nro | Atributo         | Tipode dato | Pos.In. | Pos.Fin | Descripción - Valores posibles                                    |
 *  +-----+------------------+-------------+---------+---------+-------------------------------------------------------------------+
 *  | 1   | Código Registro  | N(1)        | 1       | 1       | Código de Registro. Valor fijo: 0. Siempre es 0 e indica que este |
 *  |     |                  |             |         |         | renglón es el header.                                             |
 *  +-----+------------------+-------------+---------+---------+-------------------------------------------------------------------+
 *  | 2   | Código Banelco   | N(3)        | 2       | 4       | Identificador Banelco. Valor fijo: 400. Siempre es 400 e indica   |
 *  |     |                  |             |         |         | que el ente recaudador es Banelco.                                |
 *  +-----+------------------+-------------+---------+---------+-------------------------------------------------------------------+
 *  | 3   | Código Empresa   | N(4)        | 5       | 8       | Nro. de empresa asignado por Banelco. Son los cuatro dígitos que  |
 *  |     |                  |             |         |         | figuran en el mail de “Confirmación de aprobación de solicitud”   |
 *  |     |                  |             |         |         | que recibe la empresa.                                            |
 *  +-----+------------------+-------------+---------+---------+-------------------------------------------------------------------+
 *  | 4   | Fecha de Archivo | N(8)        | 9       | 16      | Fecha de generación del archivo. Debe coincidir con la fecha del  |
 *  |     |                  |             |         |         | nombre del archivoFormato: AAAAMMDD                               |
 *  +-----+------------------+-------------+---------+---------+-------------------------------------------------------------------+
 *  | 5   | Filler           | AN(84)      | 17      | 100     | Campo para uso futuro. Valor fijo: ceros.                         |
 *  +-----+------------------+-------------+---------+---------+-------------------------------------------------------------------+
 *
 *  DETALLE
 *  +-----+----------------------+-------------+---------+---------+------------------------------------------------------------------------
 *  | Nro | Atributo             | Tipode dato | Pos.In. | Pos.Fin | Descripción - Valores posibles                                         |
 *  +-----+----------------------+-------------+---------+---------+------------------------------------------------------------------------+
 *  | 1   | Código Registro      | N(1)        | 1       | 1       | Código de Registro. Valor fijo: 5.Indica que este renglón              |
 *  |     |                      |             |         |         | forma parte del detalle.                                               |
 *  +-----+----------------------+-------------+---------+---------+------------------------------------------------------------------------+
 *  | 2   | Nro.Referencia       | AN(19)      | 2       | 20      | Identificación del cliente en la empresa. Se refiere a la              |
 *  |     |                      |             |         |         | identificación que deberá ingresar el cliente para poder               |
 *  |     |                      |             |         |         | pagar, que le sirve a la empresa para saber quien le está              |
 *  |     |                      |             |         |         | pagando. Son los números que componen el “concepto utilizado           |
 *  |     |                      |             |         |         | como ID del cliente” que la empresa completó en el formulario          |
 *  |     |                      |             |         |         | de adhesión.                                                           |
 *  +-----+----------------------+-------------+---------+---------+---------------------------------------------------------------         +
 *  | 3   | Id.Factura           | AN(20)      | 21      | 40      | Identificación de la factura. Se refiere a la identificación           |
 *  |     |                      |             |         |         | particular de la factura que está pagando el cliente. No               |
 *  |     |                      |             |         |         | tiene que ser obligatoriamente el “Nro. de Factura”, sino              |
 *  |     |                      |             |         |         | que puede ser cualquier número que utilice la empresa                  |
 *  |     |                      |             |         |         | para individualizar el pago (puede que para un mismo                   |
 *  |     |                      |             |         |         | Nro. Referencia, haya varios Id. Factura, si un cliente                |
 *  |     |                      |             |         |         |  tiene varias facturas a pagar).                                       |
 *  | 4   | Fecha de vencimiento | N(8)        | 41      | 48      | Fecha del primer vencimiento de la factura abonada, independientemente |
 *  |     |                      |             |         |         |  de si la empresa trabaja con más vencimientos.En caso de ser un pago  |
 *  |     |                      |             |         |         | sin factura, se rellena con ceros.Formato: AAAAMMDD                    |
 *  +-----+----------------------+-------------+---------+---------+------------------------------------------------------------------------+
 *  | 5   | Código moneda        | N(1)        | 49      | 49      | Código de moneda de los importes informados.Valor fijo: 0 (Pesos).     |
 *  |     |                      |             |         |         | Siempre es  0 e indica que la factura es en pesos.                     |
 *  +-----+----------------------+-------------+---------+---------+------------------------------------------------------------------------+
 *  | 6   | Fecha  aplicación    | N(8)        | 50      | 57      | Fecha en que se cobró la factura.Formato: AAAAMMDD                     |
 *  +-----+----------------------+-------------+---------+---------+------------------------------------------------------------------------+
 *  | 7   | Importe              | N(11)       | 58      | 68      | Importe abonado. Formato: 9 enteros, 2 decimales, sin separadores.     |
 *  +-----+----------------------+-------------+---------+---------+------------------------------------------------------------------------+
 *  | 8   | Código movimiento    | N(1)        | 69      | 69      | Código de movimiento.Valores posibles:1 = Pago sin factura.            |
 *  |     |                      |             |         |         | 2 = Pago con factura.                                                  |
 *  +-----+----------------------+-------------+---------+---------+------------------------------------------------------------------------+
 *  | 9   | Fecha acreditación   | N(8)        | 70      | 77      | Fecha en que se le acreditan los fondos a la empresa. Debe coincidir   |
 *  |     |                      |             |         |         | con la fecha del nombre del archivo, del header y del trailer.         |
 *  |     |                      |             |         |         | Formato: AAAAMMDD                                                      |
 *  +-----+----------------------+-------------+---------+---------+------------------------------------------------------------------------+
 *  | 10  | Canal pago           | AN(2)       | 78      | 79      | Canal de pago por el cual se abonó la factura.                         |
 *  |     |                      |             |         |         | Valores posibles:PC = PagomiscuentasHB = Home BankingS1 = ATM          |
 *  +-----+----------------------+-------------+---------+---------+------------------------------------------------------------------------+
 *  | 11  | Nro Control          | AN(4)       | 80      | 83      | Número de control generado por Banelco, informado en el ticket.        |
 *  +-----+----------------------+-------------+---------+---------+------------------------------------------------------------------------+
 *  | 12  | Código Provincia     | AN(3)       | 84      | 86      | Si la cobranza se realizó por medio de ATM, se informa el              |
 *  |     |                      |             |         |         | código de provincia de la terminal. Caso contrario se                  |
 *  |     |                      |             |         |         | rellena con espacios.                                                  |
 *  +-----+----------------------+-------------+---------+---------+------------------------------------------------------------------------+
 *  | 13  | Filler               | AN(14)      | 87      | 100     | Campo para uso futuro. Valor fijo: ceros.                              |
 *  +-----+----------------------+-------------+---------+---------+------------------------------------------------------------------------+
 *
 *  TRAILER
 *  +-----+----------------------------+-------------+---------+---------+------------------------------------------------------------------+
 *  | Nro | Atributo                   | Tipode dato | Pos.In. | Pos.Fin | Descripción - Valores posibles                                   |
 *  +-----+----------------------------+-------------+---------+---------+------------------------------------------------------------------+
 *  | 1   | Código Registro            | N(1)        | 1       | 1       | Código de Registro. Valor fijo: 9.Siempre es  9 e indica que este|
 *  |     |                            |             |         |         | renglón es el trailer.                                           |
 *  +-----+----------------------------+-------------+---------+---------+------------------------------------------------------------------+
 *  | 2   | Código Banelco             | N(3)        | 2       | 4       | Identificador Banelco. Valor fijo: 400. Siempre es 400 e indica  |
 *  |     |                            |             |         |         | que el ente recaudador es Banelco.                               |
 *  +-----+----------------------------+-------------+---------+---------+------------------------------------------------------------------+
 *  | 3   | Código Empresa             | N(4)        | 5       | 8       | Nro. de empresa asignado por Banelco. Son los cuatro dígitos que |
 *  |     |                            |             |         |         | figuran en el mail de “Confirmación de aprobación de solicitud”  |
 *  |     |                            |             |         |         | que recibe la empresa.                                           |
 *  +-----+----------------------------+-------------+---------+---------+------------------------------------------------------------------+
 *  | 4   | Fecha archivo              | N(8)        | 9       | 16      | Fecha de generación del archivo. Debe coincidir con la fecha del |
 *  |     |                            |             |         |         | nombre del archivo, del header y del campo “Fecha acreditación”  |
 *  |     |                            |             |         |         | que está en el detalle. Formato: AAAAMMDD                        |
 *  +-----+----------------------------+-------------+---------+---------+------------------------------------------------------------------+
 *  | 5   | Cantidad registros pesos   | N(7)        | 17      | 23      | Cantidad de registros de detalle informados.Es la cantidad de    |
 *  |     |                            |             |         |         | renglones que tiene el detalle.                                  |
 *  +-----+----------------------------+-------------+---------+---------+------------------------------------------------------------------+
 *  | 6   | Cantidad registros dólares | N(7)        | 24      | 30      | Este campo se completa siempre con ceros ya que no se admiten    |
 *  |     |                            |             |         |         | facturas expresadas en dólares.                                  |
 *  +-----+----------------------------+-------------+---------+---------+------------------------------------------------------------------+
 *  | 7   | Total importe pesos        | N(11)       | 31      | 41      | Importe total de las cobranzas en pesos. Formato: 9 enteros,     |
 *  |     |                            |             |         |         | 2 decimales, sin separadores.                                    |
 *  +-----+----------------------------+-------------+---------+---------+------------------------------------------------------------------+
 *  | 8   | Total importes dólares     | N(11)       | 42      | 52      | Este campo se completa siempre con ceros ya que no se admiten    |
 *  |     |                            |             |         |         | facturas expresadas en dólares.                                  |
 *  +-----+----------------------------+-------------+---------+---------+------------------------------------------------------------------+
 *  | 9   | Filler                     | AN(48)      | 53      | 100     | Campo para uso futuro. Valor fijo: ceros.                        |
 *  +-----+----------------------------+-------------+---------+---------+------------------------------------------------------------------+
 *
 *
 * */