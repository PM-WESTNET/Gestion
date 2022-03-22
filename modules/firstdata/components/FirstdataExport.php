<?php

namespace app\modules\firstdata\components;

use Yii;
use yii\web\Application;
use app\modules\checkout\models\Payment;

class FirstdataExport {


    /**
     * Escribe en el archivo las lineas correspondiente
     * Recibe el export y la referencia al archivo a escribir
     */
    public static function createFile($export, $resource)
    {
        fwrite($resource, self::headerLine($export) . PHP_EOL);

        foreach($export->customers as $customer) {
            $totalImport = self::shouldContinue($customer); // return value is the TOTALIMPORT ($) for the line in case it DIDNT FAIL. Returns TRUE if failed.
            if($totalImport == true){
                continue; // skips
            }
            fwrite($resource, self::detailLine($export, $customer, abs($totalImport)) . PHP_EOL);
        }
        return $resource;

    }
    private function shouldContinue($customer){
        $payment = new Payment();
        //Si el saldo es 0 o el cliente tiene credito, no lo agregamos al archivo
        $totalImport = $payment->totalCalculationForQuery($customer->customer_id);
        // if import is less than 0
        if ($totalImport >= 0) return true;

        $card = CustomerDataHelper::getCustomerCreditCard($customer->code);
        // if card is not valid
        if ($card === false) {
            if (Yii::$app instanceof Application) {
                Yii::$app->session->addFlash('error', Yii::t('app','Customer data not found . Customer : {code}', ['code' => $customer->code]));
            }
            return true;
        }

        // if no error, returns amount value
        return $totalImport;
    }
    /**
     * Devuelve la linea de cabecera para el archivo
     */
    private static function headerLine($export)
    {   
        $payment = new Payment();

        $totalImport = 0;
        $cantidad = 0;
        foreach($export->customers as $customer) {
            $import = self::shouldContinue($customer); // return value is the TOTALIMPORT ($) for the line in case it DIDNT FAIL. Returns TRUE if failed.
            if($import == true){
                continue; // skips
            }

            $totalImport += $import;
            $cantidad++;
            
        }

        $commerce = str_pad($export->firstdataConfig->commerce_number, 8, '0', STR_PAD_LEFT);
        $register = "1";
        $date = date('dmy', strtotime(Yii::$app->formatter->asDate($export->presentation_date, 'yyyy-MM-dd')));
        $regiter_count = str_pad($cantidad, 7, '0', STR_PAD_LEFT);
        $signo = '0';

        $totalint = floor($totalImport);
        $import1 = str_pad($totalint, 12, '0', STR_PAD_LEFT);
        $import2 = round(($totalImport - $totalint),2) * 100;

        if ($import2 < 10) {
            $import2 = str_pad($import2, 2, '0', STR_PAD_LEFT);
        }

        $filler = str_pad('', 90);
        //* 24590100,1,070322,0002013,0,000005139593,32                                                                                          
        return $commerce.$register.$date.$regiter_count.$signo.$import1.$import2.$filler;

    }

    /**
     * Devuelve la linea correspondiente al detalle del comprobante recibido
     */
    private static function detailLine($export, $customer, $totalImport)
    {
        $payment = new Payment();

        $commerce = str_pad($export->firstdataConfig->commerce_number, 8, '0', STR_PAD_LEFT);
        $register = "2";
        $card = CustomerDataHelper::getCustomerCreditCard($customer->code);
        $reference = str_pad($customer->code, 12, '0', STR_PAD_LEFT);
        $quote = "001";
        $plan_quotes = "999";
        $frecuency = "01";

        $totalImport = round($totalImport,2);
        $totalint = floor($totalImport);

        $import1 = str_pad($totalint, 9, '0', STR_PAD_LEFT);
        $import2 = round(($totalImport - $totalint),2) * 100;
    
        if ($import2 < 10) {
            $import2 = str_pad($import2, 2, '0', STR_PAD_LEFT);
        }

        $period = date('My');
        $filler1 = " ";
        $vto = date('dmy', strtotime(Yii::$app->formatter->asDate($export->due_date, 'yyyy-MM-dd')));
        $aux = str_pad(' ', 40, ' ');
        $filler2 = str_pad('', 20);

        //* 24590100,2,4593540002636645000000133424,001,999,01,000002299,00,Mar22 070322
        return $commerce.$register.$card.$reference.$quote.$plan_quotes.$frecuency.$import1.$import2
            .$period.$filler1.$vto.$aux.$filler2;

    }
}
