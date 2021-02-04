<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 8/06/18
 * Time: 16:01
 */

namespace app\modules\checkout\components;

use app\modules\checkout\models\PagoFacilTransmitionFile;
use app\modules\pagomiscuentas\models\PagomiscuentasFile;
use Yii;

class PagoFacilReader
{
    public static function parse(PagoFacilTransmitionFile $pagoFacilTransmitionFile)
    {
        $file = null;
        $datas = [];
        $total = 0;

        try {
            $file = fopen(Yii::getAlias('@webroot') ."/".$pagoFacilTransmitionFile->file_name, 'r');
            while ($line = fgets($file)) {
                    $array_line = str_split($line);

                    //Depende del valor del primer caracter de la linea actual se deduce para que sirve.
                    //Si el primer caracter es 1 estamos en la cabecera del archivo, de lo contrario si es 5 estamos en el inicio de un pago

                    if($array_line[0] == '1') {
                        // El encabezado posee 2 lineas, como sabemos que estamos en la 1ra linea, la siguiente linea tambien es del encabezado
                        $next_line = fgets($file); // El encabezado posee 2 lineas, como sabemos que estamos en la 1ra linea, la siguiente linea tambien es del encabezado
                    }

                    if($array_line[0] == '5') {
                        $date = substr($line, 64, 4)."-".substr($line, 68, 2)."-".substr($line, 70, 2);
                        $customer_id = '';
                        $amount = '';

                        //DEFINO CLIENTE
                        $line = fgets($file); // El pago posee 3 lineas. Obtengo la segunda para definir el cliente
                        $array_line = str_split($line);
                        for ($j = 5; $j < 13; $j++) {// recorro la linea y extraigo el codigo del cliente,
                            //el codigo del cliente va desde el caracter 5 al 12 de la segunda linea
                            $customer_id .= $array_line[$j];
                        }

                        //DEFINO MONTO
                        $line = fgets($file); // Obtengo la tercer linea del pago
                        $array_line = str_split($line);
                        // recorro la linea desde el caracter 4, el cual indica la forma de pago
                        for ($k = 4; $k < 100; $k++) {
                            //el monto del pago va desde el caracter 85 al 99 de la misma linea. Una vez que obtengo la forma de pago,
                            //salto directamente al caracter 84 para que al iterar nuevamente me posicione en el caracter 85
                            if ($array_line[$k] == 'E' || $array_line[$k] == 'P') { // EFECTIVO O DEBITO
                                $payment_method = "Pago Facil";
                                $k = 84;
                            } else {
                                if ($k !== 98) { // El punto decimal no esta contemplado en la linea, pero debe ir antes del caracter 98
                                    $amount .= $array_line[$k];
                                } else {
                                    $amount .= '.' . $array_line[$k];
                                }
                            }
                        }

                        $data = [
                            'date'            => $date,
                            'customer_id'     => (int) $customer_id,
                            'amount'          => (float) $amount,
                            'payment_method'  => $payment_method  ? $payment_method  : '',
                            'payment_id'      => '',
                        ];

                        $datas[] = $data;
                        $total += (float) $amount;
                    }
            }
        } catch (\Exception $ex){
            error_log($ex->getMessage());
        }

        if($file) {
            fclose($file);
        }
        return $datas;
    }
}