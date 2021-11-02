<?php

namespace app\modules\automaticdebit\components;

use app\modules\sale\models\Bill;
use app\modules\automaticdebit\models\AutomaticDebit;
use app\modules\automaticdebit\models\BankCompanyConfig;
use app\modules\automaticdebit\models\BillHasExportToDebit;
use app\modules\automaticdebit\models\DebitDirectImport;
use app\modules\checkout\models\Payment;
use app\modules\checkout\models\PaymentMethod;
use app\modules\sale\models\Company;
use app\modules\sale\models\Customer;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Transaction;

class BancoSuperville implements BankInterface
{


    /**
     *  Debe exportar el archivo para debito directo
     * @param $company_id
     * @return mixed
     */
    public function export($export)
    {
        $companyConfig = BankCompanyConfig::findOne(['company_id' => $export->company_id, 'bank_id' => $export->bank_id]);

        if (empty($companyConfig)) {
            Yii::$app->session->addFlash('error', Yii::t('app','Company not configured'));
            return false;
        }

        $this->companyConfig = $companyConfig;

        if(!file_exists(\Yii::getAlias('@app').'/web/direct_debit/')) {
            mkdir(\Yii::getAlias('@app').'/web/direct_debit/', 0777);
        }

        $export->save();

        $filename = 'billsvca'.date('dm').'.'.$export->direct_debit_export_id;

        $file = \Yii::getAlias('@app').'/web/direct_debit/'.$filename;

        $resource = fopen($file, 'w+');

        if ($resource === false) {
            return false;
        }

        $export->file = $file;
        $this->fileName = $filename;
        $export->save();

        $debits= $this->getDebits($export->bank_id, $export->company_id);

        $totalAmount = 0;
        $totalRegister = 0;
        foreach ($debits as $debit) {
            
            $bills = $this->getCustomerBills($debit->customer_id, $export->company_id, $this->periodFrom, $this->periodTo);
            if(!empty($bills)){
                $totalImport = Payment::totalCalculationForQuery($bills[0]->customer_id);
                if ($totalImport >= 0) {
                    continue;
                }

                $totalImport = abs($totalImport);
                $totalAmount = $totalAmount + $totalImport;
                $totalRegister++;

                $resource = $this->addBody($resource, $debit, $export,$totalImport);

                $bhetd = new BillHasExportToDebit(['bill_id' => $bills[0]->bill_id, 'direct_debit_export_id' => $export->direct_debit_export_id]);

                $bhetd->save();
            }           

        }

        $resource = $this->addFooterLine($resource, $totalAmount, $totalRegister);

        fclose($resource);

        return true;
    }


    public function addBody($resource, $debit, $export, $totalImport){
        $current_date = date('Y-m-d');

        $due_date = null;
        switch (date('D',strtotime($current_date . "+2 days"))) {
            case 'Sat':
                $due_date = str_pad(date('dmY',strtotime($current_date . "+4 days")),8,0,STR_PAD_LEFT);
                break;

            case 'Sun':
                $due_date = str_pad(date('dmY',strtotime($current_date . "+3 days")),8,0,STR_PAD_LEFT);
                break;
            
            default:
                $due_date = str_pad(date('dmY',strtotime($current_date . "+2 days")),8,0,STR_PAD_LEFT);
                break;
        }
        
        $type_of_newness = 'D';                                                             // Tipo de novedad
        $cuit_company = str_pad(str_replace('-','',$this->companyConfig->company->tax_identification), 11, '0', STR_PAD_LEFT); //CUIT Empresa Originante
        $sector = '001';                                                                    // Sector
        $benefit = str_pad('CUOTA',10,' ',STR_PAD_RIGHT);                                  // Prestacion
        $due_date1 = $due_date;                            // Fecha de Vencimiento 1
        $block_cbu_1 = str_pad(substr($debit->cbu, 0, 8),8,0,STR_PAD_LEFT);                 // Bloque 1 CBU
        $fixed_fields = '000';                                                              // Campos Fijos
        $block_cbu_2 = str_pad(substr($debit->cbu,8),14,0,STR_PAD_LEFT);                    // Bloque 2 CBU
        $customer_identification = str_pad($debit->customer_id,22,' ',STR_PAD_RIGHT);         // Identificación del cliente


        $original_debit_due_date = $due_date;
        $debit_reference = str_pad('FACTURA',15,' ',STR_PAD_RIGHT);

        $intamount = floor(round($totalImport, 2));
        $import = str_pad($intamount, 8, '0', STR_PAD_LEFT);
        $import_decimal = round(($totalImport - $intamount), 2) * 100 ;
        if ($import_decimal < 10) {
            $import_decimal = str_pad(abs($import_decimal), 2, 0, STR_PAD_LEFT);
        }
        $import .= str_pad($import_decimal, 2, '0', STR_PAD_LEFT);
        $debit_currency = 80;
        $due_date2 = '00000000';
        $import2 = '0000000000';

        $due_date3 = '00000000';
        $import3 = '0000000000';
        $new_payer_identifier = str_pad('',22,' ',STR_PAD_RIGHT);
        $rejection_code = str_pad('',3,' ',STR_PAD_RIGHT);

        $number_of_order = str_pad('',10,0,STR_PAD_LEFT);
        $movement_number = str_pad('',10,0,STR_PAD_LEFT);

        $filler = str_pad('',54,' ',STR_PAD_RIGHT);


        $line = $type_of_newness . 
                $cuit_company . 
                $sector . 
                $benefit . 
                $due_date1 . 
                $block_cbu_1 . 
                $fixed_fields . 
                $block_cbu_2 . 
                $customer_identification .
                $original_debit_due_date . 
                $debit_reference . 
                $import . 
                $debit_currency . 
                $due_date2 . 
                $import2 . 
                $due_date3 . 
                $import3 . 
                $new_payer_identifier .
                $rejection_code .
                $number_of_order . 
                $movement_number . 
                $filler;

        fwrite($resource, mb_convert_encoding($line.PHP_EOL, 'Windows-1252'));

        return $resource;

    }

    public function addFooterLine($resource, $totalAmount, $totalRegister){

        $totalint = floor($totalAmount);
        $import1 = str_pad($totalint, 8, '0', STR_PAD_LEFT);
        $import2 = round(($totalAmount - $totalint),2) * 100;

        if ($import2 < 10) {
            $import2 = str_pad($import2, 2, '0', STR_PAD_LEFT);
        }

        $type_of_newness = 'T';
        $number_of_total_records = str_pad($totalRegister,10,0,STR_PAD_LEFT);
        $number_of_monetary_records = str_pad($totalRegister,7,0,STR_PAD_LEFT);
        $number_of_non_monetary_records = str_pad('',7,0,STR_PAD_LEFT);
        $date_of_process = str_pad(date('dmY'),8,0,STR_PAD_LEFT);
        $filler1 = str_pad('',70,' ',STR_PAD_RIGHT);
        $sum_of_record_amounts = $import1.$import2;
        $filler2 = str_pad('',137,' ',STR_PAD_RIGHT);

        $line = $type_of_newness .
                $number_of_total_records . 
                $number_of_monetary_records . 
                $number_of_non_monetary_records . 
                $date_of_process . 
                $filler1 . 
                $sum_of_record_amounts . 
                $filler2;

        fwrite($resource, mb_convert_encoding($line.PHP_EOL, 'Windows-1252'));

        return $resource;
    }

     /**
     * @param $bank_id
     * @param $company_id
     * @return array|\yii\db\ActiveRecord[]
     * Obtine los débitos de la empresa y el banco indicados
     */
    private function getDebits($bank_id, $company_id) {
        $debits = AutomaticDebit::find()
            ->innerJoin('customer c', 'c.customer_id=automatic_debit.customer_id')
            ->andWhere([
                'bank_id' => $bank_id,
                'c.company_id' => $company_id,
                'automatic_debit.status' => AutomaticDebit::ENABLED_STATUS,
                'automatic_debit.customer_type' => $this->type
            ])->all();

        return $debits;
    }


    /**
     * @param $customer_id
     * @param null $fromDate
     * @param null $toDate
     * @return mixed
     * @throws InvalidConfigException
     * Obtiene los comprobantes de un cliente pendientes de pago.
     */
    private function getCustomerBills($customer_id, $company_id, $fromDate = null, $toDate = null)
    {
        $bills = Bill::find()
            ->leftJoin('bill_has_export_to_debit bhtd', 'bhtd.bill_id=bill.bill_id')
            ->innerJoin('bill_type bt', 'bt.bill_type_id=bill.bill_type_id')
            //->andFilterWhere(['>=', 'bill.timestamp', strtotime(\Yii::$app->formatter->asDate($fromDate, 'yyyy-MM-dd'))])
            //->andFilterWhere(['<=', 'bill.timestamp', strtotime(\Yii::$app->formatter->asDate($toDate, 'yyyy-MM-dd'))+86400])
            ->andWhere(['customer_id' => $customer_id])
            ->andWhere(['IS', 'bhtd.bill_id', null])
            //->andWhere(['bill.status' => 'closed'])
            ->andWhere(['bt.multiplier' => 1])
            ->andWhere(['LIKE', 'bt.class', 'app\modules\sale\models\bills\Bill'])
            ->andWhere(['bill.company_id' => $company_id])
            ->orderBy(['bill.bill_id' => SORT_DESC])
            ->all();

        return $bills;
    }


    /**
     * Debe importar el archivo con los pagos y crear los pagos correspondientes
     * @return mixed
     */
    public function import($resource, $import, $fileName)
    {

        $coelsa_code = [
            'ACE' => 'Aceptado',
            'R02' => 'CUENTA CERRADA POR ORDEN JUDICIAL',
            'R03' => 'CUENTA INEXISTENTE',
            'R04' => 'DIGITO VERIFICADOR DE CBU INCORRECTO',
            'R08' => 'STOP DEBIT',
            'R10' => 'FALTA DE FONDOS',
            'R13' => 'SUCURSAL/ENTIDAD DESTINO INEXISTENTE',
            'R14' => 'CLIENTE INEXISTENTE',
            'R15' => 'ADHERENTE DADO DE BAJA',
            'R17' => 'ERROR DE FORMATO',
            'R18' => 'FECHA DE COMPENSACION ERRONEA',
            'R19' => 'IMPORTE ERRONEO',
            'R20' => 'MONEDA DIFERENTE A LA DE LA CUENTA',
            'R22' => 'DEVOLUCION POR SOLICITUD DEL BENEFICIARIO',
            'R23' => 'SUCURSAL NO HABILITADA',
            'R24' => 'TRANSACCION DUPLICADA',
            'R25' => 'ERROR EN REGISTRO ADICIONAL',
            'R26' => 'ERROR POR CAMPO MANDATARIO',
            'R27' => 'ERROR EN CONTADOR DE REGISTRO',
            'R29' => 'REVERSION YA EFECTUADA',
            'R31' => 'VUELTA ATRAS DE CAMARA',
            'R75' => 'FECHA INVALIDA',
            'R76' => 'ERROR EN EL CUIT O DIGITO VERIFICADOR',
            'R77' => 'ERROR EN CAMPO 4 REG.INDIVID.',
            'R78' => 'ERROR EN CAMPO 5 REG.INDIVID.',
            'R79' => 'ERROR EN REF.UNIVOCA TRANSFERENC.',
            'R80' => 'ERROR CAMPO 3 REG.ADIC.(CONCEPTO)',
            'R87' => 'MONEDA INVALIDA',
            'R88' => 'ERROR EN CAMPO 2 REG.INDIVID.',
            'R89' => 'ERRORES EN ADHESION',
            'R90' => 'TRANSACC.NO CORRESP: NO EXSTE ORIGINAL',
            'R91' => 'COD.ENTIDAD.INCOMPAT.CON MONEDA TXN',
            'R93' => 'DIA NO LABORABLE',
            'R98' => 'SOLICITUD ENTIDAD ORIGINANTE' 
        ];

        $companyConfig = null;
        $proccess_timestamp = null;
        $payments = [];
        $failed_payment_register_created = 0;
        $failed_payments = [];

        while($line = fgets($resource)){
            if(substr($line,0 , 1) == 'T'){
                $companyConfig = BankCompanyConfig::findOne(['company_id' => $import->company_id]);
                if (empty($companyConfig)) {
                    Yii::$app->session->addFlash('error', Yii::t('app','Company not configured'));
                    return false;
                }
                $process_timestamp = substr($line, 29, 4).'-'.substr($line, 27, 2).'-'.substr($line,25, 2);
                break;
            }
        }
        fseek($resource,0);

        while ($line = fgets($resource)) {
            if(substr($line,0,1) == "T")
                break;


            $customer_id = trim(substr($line, 58, 22));
            $amount = (double) substr($line, 103, 8).'.'.substr($line,111,2);
            $cbu = substr($line, 33, 8);
            $code = substr($line, 173, 3);
            $code_description = $coelsa_code[$code];
            $customer_code = Customer::findOne(['customer_id' => $customer_id])->code;

            if($code == 'ACE'){
                $payments[] = [
                    'customer_code' => $customer_code,
                    'amount' => $amount,
                    'date' =>  $process_timestamp,
                    'cbu' => $cbu,
                ];
                 
            }else{
                $failed_payment_register_created ++;
                array_push($failed_payments, ['customer_code' => $customer_code, 'amount' => $amount, 'date' => $process_timestamp, 'cbu' => $cbu, 'description' => $code_description]);
            }
        }

        $import->process_timestamp = strtotime($process_timestamp);
        $import->file = $fileName;
        $import->save();
        $this->createFailedPayments($failed_payments, $import->debit_direct_import_id);

        $result = $this->createPayments($payments, $companyConfig->company, $import);

        return [
            'status' => true,
            'errors' => 'Payment created; '.$result['payments_created'].'. Payment failed: '.$result['payments_failed'].' Rejected payment register created: '.$failed_payment_register_created,
            'created_payments' => $result['payments_created'],
            'failed_payments' => $result['payments_failed'],
            'rejected_payment_register_created' => $failed_payment_register_created,
        ];
    }

    /**
     * @param $failed_payments
     * @param $import_id
     * Crea registros de pagos fallidos
     */
    public function createFailedPayments($failed_payments, $import_id)
    {
        foreach ($failed_payments as $failed_payment) {
            DebitDirectImport::createFailedPayment($failed_payment['customer_code'], $failed_payment['amount'], $failed_payment['date'], $failed_payment['cbu'], $import_id, $failed_payment['description']);
        }
    }

    /**
     * @param $payments
     * @param Company $company
     * @param $import
     * @return array
     * @throws InvalidConfigException
     * @throws \yii\db\Exception
     * Crea los pagos y los relaciona al import, De no poder crearlos, registra el error como un nuevo DebitDirectImportFailedPayment model
     */
    private function createPayments($payments, Company $company, $import)
    {
        $payment_method = PaymentMethod::findOne(['name' => 'Débito Directo']);
        $payments_created = 0;
        $failed_payments = [];

        foreach ($payments as $payment) {
            $customer = Customer::findOne(['code' => $payment['customer_code']]);
            $transaction = Yii::$app->db->beginTransaction();

            if (!empty($customer)) {
                $p = new Payment([
                    'customer_id' => $customer->customer_id,
                    'amount' => $payment['amount'],
                    'partner_distribution_model_id' => $company->partner_distribution_model_id,
                    'company_id' => $company->company_id,
                    'date' => (new \DateTime('now'))->format('Y-m-d')
                ]);

                if ($p->save()) {
                    $this->createItemAndRelation($payment['amount'], 'Debito Directo cta ' . $payment['cbu'], $payment_method->payment_method_id, $import->money_box_account_id, $p, $import);
                    $payments_created ++;
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                    array_push($failed_payments, ['customer_code' => $payment['customer_code'], 'amount' => $payment['amount'], 'date' => $payment['date'], $import->process_timestamp, 'cbu' => $payment['cbu'], 'import_id' => $import->debit_direct_import_id, 'description' => Yii::t('app', 'Cant create payment. Customer code: ').$payment['customer_code']]);
                }
            } else {
                $transaction->rollBack();
                array_push($failed_payments, ['customer_code' => $payment['customer_code'], 'amount' => $payment['amount'] ,'date' => $payment['date'], $import->process_timestamp, 'cbu' => $payment['cbu'], 'import_id' => $import->debit_direct_import_id, 'description' => Yii::t('app', 'Customer not found'). ': '.$payment['customer_code']]);
            }

        }

        //Creamos registros de todos los pagos que no se pudieron procesar
        $this->createFailedPayments($failed_payments, $import->debit_direct_import_id);

        return [
            'success' => true,
            'payments_created' => $payments_created,
            'payments_failed' => count($failed_payments),
        ];
    }

    /**
     * @param $amount
     * @param $description
     * @param $payment_method_id
     * @param $money_box_account_id
     * @param Payment $payment
     * @param $import
     * @return mixed
     * Crea el item de pago, lo asocia al pago y crea la relacion entre el pago y el import
     */
    private function createItemAndRelation($amount, $description, $payment_method_id, $money_box_account_id, Payment $payment, $import)
    {
        $payment_item = [
            'amount'=> $amount,
            'description'=> $description,
            'payment_method_id'=> $payment_method_id,
            'money_box_account_id'=> $money_box_account_id,
            'payment_id' => $payment->payment_id,
            'paycheck_id' => null
        ];
        $payment->addItem($payment_item);
        return $import->createPaymentRelation($payment->payment_id);
    }

}
