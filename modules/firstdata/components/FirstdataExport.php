<?php

namespace app\modules\firstdata\components;

use Yii;


class FirstdataExport {


    /**
     * Escribe en el archivo las lineas correspondiente
     * Recibe el export y la referencia al archivo a escribir
     */
    public static function createFile($export, $resource)
    {
        fwrite($resource, self::headerLine($export) . PHP_EOL);

        foreach($export->bills as $bill) {
            fwrite($resource, self::detailLine($export, $bill) . PHP_EOL);
        }

        return $resource;

    }

    private static function headerLine($export)
    {
        $commerce = str_pad($export->firstdataConfig->commerce_number, 8, '0', STR_PAD_LEFT);
        $register = "1";
        $date = date('dmy', strtotime(Yii::$app->formatter->asDate($export->presentation_date, 'yyyy-MM-dd')));
        $regiter_count = str_pad(count($export->bills), 7, '0', STR_PAD_LEFT);
        $signo = '0';

        $totalImport = $export->totalImport;
        $totalint = floor($totalImport);
        $import1 = str_pad($totalint, 12, '0', STR_PAD_LEFT);
        $import2 = round(($totalImport - $totalint),2) * 100;

        if ($import2 < 10) {
            $import2 = str_pad($import2, 2, '0', STR_PAD_LEFT);
        }

        $filler = str_pad('', 90);

        return $commerce.$register.$date.$regiter_count.$signo.$import1.$import2.$filler;

    }

    private static function detailLine($export, $bill)
    {
        $commerce = str_pad($export->firstdataConfig->commerce_number, 8, '0', STR_PAD_LEFT);
        $register = "2";
        $card = CustomerDataHelper::getCustomerCreditCard($bill->customer);
        $reference = str_pad($bill->customer->code, 12, '0', STR_PAD_LEFT);
        $quote = "001";
        $plan_quotes = "999";
        $frecuency = "01";
        
        $totalImport = $bill->total;
        $totalint = floor($totalImport);
        $import1 = str_pad($totalint, 9, '0', STR_PAD_LEFT);
        $import2 = round(($totalImport - $totalint),2) * 100;

        if ($import2 < 10) {
            $import2 = str_pad($import2, 2, '0', STR_PAD_LEFT);
        }

        $period = date('my');
        $filler1 = " ";
        $vto = date('dmy', strtotime(Yii::$app->formatter->asDate($export->due_date, 'yyyy-MM-dd')));
        $aux = str_pad(' ', 40, ' ');
        $filler2 = str_pad('', 20);


        return $commerce.$register.$card.$reference.$quote.$plan_quotes.$frecuency.$import1.$import2
            .$period.$filler1.$vto.$aux.$filler2;

    }
}