<?php

namespace app\modules\firstdata\components;

use app\modules\sale\models\Customer;
use app\modules\checkout\models\Payment;
use app\modules\checkout\models\PaymentMethod;
use app\modules\firstdata\models\FirstdataImportPayment;

class FirstdataImport 
{

    public static function processFile($import)
    {
        $resource = fopen($import->response_file, 'r');
        $paymentMethod = PaymentMethod::findOne(['name' => 'Firstdata']);

        while($line = fgets($resource)) {
            $type = substr($line, 2, 1);
            switch ($type) {
                case "1":
                    $total = (double)(substr($line, 39, 10) . '.' . substr($line, 49, 2));
                    $registers = (integer)substr($line, 32, 6);

                    $import->updateAttributes([
                        'total' => $total,
                        'registers' => $registers
                    ]);
                break;
                case "2":
                        $customer_code = (integer)substr($line, 26, 12);
                        $customer = Customer::findOne(['code' => $customer_code]);
                        $amount = (double)(substr($line, 40, 9) . '.' . substr($line, 49, 2));
                        $reject_code = substr($line, 58, 2);
                        $period = substr($line, 60, 5);

                        if ($customer) {
                            self::createPayment($customer, $amount, $reject_code,$period, $import, $paymentMethod->payment_method_id);
                        } else {
                            self::createImportPayment($import, $amount, $customer_code);
                        }
                break;
            }
        }
    }


    private static function createPayment($customer, $amount, $reject_code, $period, $import, $payment_method_id) 
    {
        if ($reject_code === '00') {
            $payment = new Payment([
                'customer_id' => $customer->customer_id,
                'amount' => $amount,
                'partner_distribution_model_id' => $customer->company->partner_distribution_model_id,
                'company_id' => $customer->company->company_id,
                'date' => (new \DateTime('now'))->format('Y-m-d')
            ]);

            if ($payment->save()) {
                $payment->addItem([
                    'amount'=> $amount,
                    'description'=> $period,
                    'payment_method_id'=> $payment_method_id,
                    'money_box_account_id'=> $import->money_box_account_id,
                    'payment_id' => $payment->payment_id,
                    'paycheck_id' => null
                ]);

                return self::createImportPayment($import, $amount, $customer->code, $payment->payment_id);
            }
        } else {
            self::createImportPayment($import, $amount, $customer->code, null, "100");
        }
    }

    private static function createImportPayment($import, $amount, $customer_code, $payment_id = null, $error = null)
    {
        $importPayment = new FirstdataImportPayment([
            'firstdata_import_id' => $import->firstdata_import_id,
            'payment_id' => $payment_id,
            'customer_code' => $customer_code,
            'amount' => $amount,
            'status' => 'pending',
            'error' => $error !== "00" ? self::getErrorMessage($error) : null
        ]);

        return $importPayment->save();
    }

    private static function getErrorMessage($code) 
    {
        $errors = [
            '01' => "Comercio informado no existe o dado de baja / Marca de la tarjeta no habilitada para el comercio.",
            '13' => "Falta importe de Débito",
            '14' => "Importe del débito invalido",
            '15' => "Adhesión dada de baja",
            '17' => "Cuota referencia ya fue ingresada",
            '50' => "Causa de rechazo en boletín",
            '61' => "Socio dado de baja en maestro",
            '62' => "Tarjeta vencida",
            '63' => "Cantidad de cuotas del plan inválida",
            '64' => "Tarjeta privada en comercio no autorizado",
            '65' => "Tarjeta no vigente",
            '66' => "Tarjeta inexistente",
            '72' => "Cuota inicial invalida",
            '73' => "Frecuencia de debilitación inválida",
            '75' => "Número de referencia inválido",
            '81' => "Comercio no autorizado a operar en dólares",
            '83' => "Entidad Pagadora inexistente",
            '85' => "Stop Debit",
            '86' => "Autorización inexistente o rechazada",
            '87' => "Importe supera tope /débito acotado",
            '88' => "Autorización rechazada socio en mora",
            '89' => "Autorización rechazada socio Líder",
            '90' => "Imp. Cupón crédito supera suma ult. deb",
            '91' => "Adh. Inexstente para cupón crédito",
            '92' => "Socio internacional p/cupón crédito",
            '100' => "Cliente Inválido"
        ];

        return $errors[$code];
    }

}