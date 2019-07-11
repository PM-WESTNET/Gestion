<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 10/05/19
 * Time: 18:00
 */

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

class BancoFrances implements BankInterface
{

    public $companyConfig;
    public $debitAutomatics;
    public $processTimestamp;
    public $paymentsData;
    public $periodFrom;
    public $periodTo;
    public $type;
    private $fileName;

    /**
     *  Debe exportar el archivo para debito directo
     * @param $company_id
     * @return mixed
     */
    public function export($export)
    {
        $companyConfig = BankCompanyConfig::findOne(['company_id' => $export->company_id, 'bank_id' => $export->bank_id]);

        if (empty($companyConfig)) {
            throw new InvalidConfigException('Company not configured for bank');
        }

        $this->companyConfig = $companyConfig;

        $d= (($this->type === 'own') ? 1 : 0);
        $filename = rand(1000000,9999999). $d;
        if(!file_exists(\Yii::getAlias('@app').'/web/direct_debit/')) {
            mkdir(\Yii::getAlias('@app').'/web/direct_debit/', 0777);
        }
        $file = \Yii::getAlias('@app').'/web/direct_debit/'.$filename;

        $resource = fopen($file.'.txt', 'w');

        if ($resource === false) {
            return false;
        }

        $export->file = $file.'.txt';
        $this->fileName = $filename.'.txt';
        $export->save();

        $debits= $this->getDebits($export->bank_id, $export->company_id);

        $resource = $this->addHeader($resource);
        $totalRegister = 1;
        $totalOperations = 0;
        $totalAmount = 0;
        foreach ($debits as $debit) {
            $bills = $this->getCustomerBills($debit->customer_id, $this->periodFrom, $this->periodTo);

            foreach ($bills as $bill) {
                $resource = $this->addFirstLine($resource, $debit, $bill, $export->concept);
                $resource = $this->addSecondLine($resource, $debit);
                $resource = $this->addThirdLine($resource, $debit);
                $resource = $this->addConceptLine($resource, $debit);

                $totalRegister = $totalRegister + 4;
                $totalOperations++;
                $totalAmount = $totalAmount + $bill->total;

                $bhetd = new BillHasExportToDebit(['bill_id' => $bill->bill_id, 'direct_debit_export_id' => $export->direct_debit_export_id]);

                $bhetd->save();
            }

        }

        $totalRegister++;
        $resource = $this->addFooterLine($resource, $totalAmount, $totalOperations, $totalRegister);

        fclose($resource);

        return true;
    }

    /**
     * Debe importar el archivo con los pagos y crear los pagos correspondientes
     * @return mixed
     */
    public function import($resource, $import)
    {
        $companyConfig = null;
        $proccess_timestamp = null;
        $payments = [];
        $failed_payment_register_created = 0;
        $failed_payments = [];

        while ($line = fgets($resource)){
            $code_line = substr($line,0,4);

            switch($code_line) {
                case '4110':
                    $companyConfig = BankCompanyConfig::findOne(['company_id' => $import->company_id]);

                    if (empty($companyConfig)) {
                        throw new InvalidConfigException('Company not configured');
                    }

                    $process_timestamp = substr($line, 17, 8);
                    break;
                case '4210':
                    $customer_code = substr($line, 11, 22);
                    $int_amount = substr($line, 55, 13);
                    $decimal_amount = substr($line, 68, 2);
                    $amount = (double) ($int_amount.'.'.$decimal_amount);
                    $code = substr($line, 70, 2);
                    $cbu = substr($line, 33,22);
                    $code_description = substr($line, 124,40);
                    \Yii::trace('$code');
                    \Yii::trace($code);

                    if($code === '00') {
                        $payments[] = [
                            'customer_code' => ltrim($customer_code, '0'),
                            'amount' => $amount,
                            'date' =>  $this->restoreDate($proccess_timestamp),
                            'cbu' => $cbu,
                        ];
                    } else {
                        $failed_payment_register_created ++;
                        array_push($failed_payments, ['customer_code' => $customer_code, 'amount' => $amount, 'date' => $this->restoreDate($process_timestamp), 'cbu' => $cbu, 'description' => $code_description]);
                   }
                    break;
            }
        }
        $import->process_timestamp = strtotime($this->restoreDate($process_timestamp));
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
            $transaction->begin();

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
                } else {
//                    $transaction->rollBack();
                    \Yii::trace($p->getErrors());
                    array_push($failed_payments, ['customer_code' => $payment['customer_code'], 'amount' => $payment['amount'], 'date' => $payment['date'], $import->process_timestamp, 'cbu' => $payment['cbu'], 'import_id' => $import->debit_direct_import_id, 'description' => Yii::t('app', 'Cant create payment. Customer code: ').$payment['customer_code']]);
                }
            } else {
                $transaction->rollBack();
                array_push($failed_payments, ['customer_code' => $payment['customer_code'], 'amount' => $payment['amount'] ,'date' => $payment['date'], $import->process_timestamp, 'cbu' => $payment['cbu'], 'import_id' => $import->debit_direct_import_id, 'description' => Yii::t('app', 'Customer not found'). ': '.$payment['customer_code']]);
            }

            $transaction->commit();
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


    /**
     * @param $resource
     * @return mixed
     * Agrega el header al archivo
     */
    private function addHeader($resource)
    {
        $register_code = '4110';
        $companyId = $this->getCompanyIdentification();
        $createDate = date('Ymd');
        $processDate = date('Ymd', $this->processTimestamp);
        $bank = '0017';
        $branch = $this->companyConfig->branch;
        $dc= $this->companyConfig->control_digit;
        $account= $this->companyConfig->account_number;
        $service = $this->getServiceCode();
        $divisa = 'ARS';
        $devolucion = '0';
        $file= $this->fileName;
        $ord = str_pad($this->companyConfig->company->name, 36, ' ');
        $tipoCBU= '20';
        $libre = str_pad(' ', 141, ' ');

        $line = $register_code.$companyId.$createDate.$processDate.$bank.$branch.$dc.$account.$service.$divisa.$devolucion.$file.$ord.$tipoCBU.$libre;

        fwrite($resource, mb_convert_encoding($line.PHP_EOL, 'Windows-1252'));

        return $resource;

    }

    /**
     * @param $resource
     * @param AutomaticDebit $beneficiary
     * @param Bill $bill
     * Añade la primera línea
     */
    private function addFirstLine($resource, $beneficiary, $bill, $concept)
    {
        $register_code = '4210';
        $companyId = $this->getCompanyIdentification();
        $free1= str_pad(' ', 2, ' ');
        $beneficiary_number = $beneficiary->beneficiario_number;
        $cbu = $beneficiary->cbu;

        $intamount = floor($bill->total);

        $import1 = str_pad($intamount, 13, '0', STR_PAD_LEFT);
        $import2 = round(($bill->total - $intamount), 2) * 100;
        $code_dev = str_pad(' ', 6, ' ');
        $ref = str_pad($concept, 22, ' ');
        $fecha = date('Ymd', $this->processTimestamp);
        $free2 = str_pad(' ', 2, ' ');
        $bill_number = str_pad($bill->number, 15, '0', STR_PAD_LEFT);
        $status_dev = str_pad(' ', 1, ' ');
        $descr_dev = str_pad(' ', 40, ' ');
        $free3 = str_pad(' ', 86, ' ');

        $line = $register_code.$companyId.$free1.$beneficiary_number.$cbu.$import1.$import2.$code_dev.$ref.$fecha.$free2.$bill_number.$status_dev.$descr_dev.$free3;

        fwrite($resource, mb_convert_encoding($line.PHP_EOL, 'Windows-1252'));

        return $resource;

    }

    /**
     * @param $resource
     * @param $beneficiary
     * @return mixed
     * Añade la segunda línea
     */
    private function addSecondLine ($resource, $beneficiary) {

        $register_code = '4220';
        $companyId = $this->getCompanyIdentification();
        $free1= str_pad(' ', 2, ' ');
        $beneficiary_number = $beneficiary->beneficiario_number;
        $beneficiary_name = str_pad($beneficiary->customer->fullName, 36, ' ');

        $dom1 = str_pad(' ', 36, ' ');
        $dom2 = str_pad(' ', 36, ' ');
        $free2 = str_pad(' ', 109, ' ');

        $line = $register_code.$companyId.$free1.$beneficiary_number.$beneficiary_name.$dom1.$dom2.$free2;

        fwrite($resource, mb_convert_encoding($line.PHP_EOL, 'Windows-1252'));

        return $resource;
    }

    /**
     * @param $resource
     * @param $beneficiary
     * @return mixed
     * Añade la tercer línea
     */
    private function addThirdLine ($resource, $beneficiary)
    {
        $register_code = '4230';
        $companyId = $this->companyConfig->company_identification;
        $free1= str_pad(' ', 2, ' ');
        $beneficiary_number = $beneficiary->beneficiario_number;
        $loc = str_pad(' ', 36, ' ');
        $prov = str_pad(' ', 36, ' ');
        $cp = str_pad(' ', 36, ' ');
        $free2 = str_pad(' ', 109, ' ');

        $line = $register_code.$companyId.$free1.$beneficiary_number.$loc.$prov.$cp.$free2;

        fwrite($resource, mb_convert_encoding($line.PHP_EOL, 'Windows-1252'));

        return $resource;
    }

    /**
     * @param $resource
     * @param $beneficiary
     * @return mixed
     * Añade la línea correspondiente al concepto
     */
    private function addConceptLine($resource, $beneficiary)
    {
        $register_code = '4240';
        $companyId = $this->getCompanyIdentification();
        $free1= str_pad(' ', 2, ' ');
        $beneficiary_number = $beneficiary->beneficiario_number;
        $concept = str_pad($beneficiary->customer->company->name, 40, ' ');
        $free2 = str_pad(' ', 177, ' ');

        $line = $register_code.$companyId.$free1.$beneficiary_number.$concept.$free2;

        fwrite($resource, mb_convert_encoding($line.PHP_EOL, 'Windows-1252'));

        return $resource;
    }

    /**
     * @param $resource
     * @param $total
     * @param $countOp
     * @param $countTotal
     * @return mixed
     * Añade la línea del footer
     */
    private function addFooterLine ($resource, $total, $countOp, $countTotal)
    {
        $register_code = '4910';
        $companyId = $this->getCompanyIdentification();
        $totalint = floor($total);
        $import1 = str_pad($totalint, 13, '0', STR_PAD_LEFT);
        $import2 = round(($total - $totalint),2) * 100;
        $free =  str_pad(' ', 208, ' ');
        $op = str_pad($countOp, 8, '0', STR_PAD_LEFT);
        $total = str_pad($countTotal, 10, '0', STR_PAD_LEFT);

        $line = $register_code.$companyId.$import1.$import2.$op.$total.$free;

        fwrite($resource, mb_convert_encoding($line.PHP_EOL, 'Windows-1252'));

        return $resource;
    }

    /**
     * @param $customer_id
     * @param null $fromDate
     * @param null $toDate
     * @return mixed
     * @throws InvalidConfigException
     * Obtiene los comprobantes de un cliente pendientes de pago.
     */
    private function getCustomerBills($customer_id, $fromDate = null, $toDate = null)
    {
        $bills = Bill::find()
            ->leftJoin('bill_has_export_to_debit bhtd', 'bhtd.bill_id=bill.bill_id')
            ->innerJoin('bill_type bt', 'bt.bill_type_id=bill.bill_type_id')
            ->andFilterWhere(['>=', 'bill.date', \Yii::$app->formatter->asDate($fromDate, 'yyyy-MM-dd')])
            ->andFilterWhere(['<=', 'bill.date', \Yii::$app->formatter->asDate($toDate, 'yyyy-MM-dd')])
            ->andWhere(['customer_id' => $customer_id])
            ->andWhere(['IS', 'bhtd.bill_id', null])
            ->andWhere(['bill.status' => 'closed'])
            ->andWhere(['bt.multiplier' => 1])
            ->andWhere(['bt.class' => Bill::class])
            ->all();

        return $bills;
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
     * @param $date
     * @return string
     * Obtiene la fecha
     */
    private function restoreDate($date)
    {
        \Yii::trace($date);
        $restore = substr($date,0,4) .'-'. substr($date, 4,2).'-'.substr($date,6);
        \Yii::trace('devolucion '. $restore);
        return $restore;
    }

    /**
     * @return mixed
     * Obtiene la identificaion de una empresa
     */
    private function getCompanyIdentification()
    {
        if ($this->type === 'own') {
            return $this->companyConfig->company_identification;
        }else {
            return $this->companyConfig->other_company_identification;
        }
    }

    /**
     * @return string
     * Obtiene el código del servicio
     */
    private function getServiceCode()
    {
        if ($this->type === 'own') {
            return str_pad($this->companyConfig->service_code, 10, ' ');
        }else {
            return str_pad($this->companyConfig->other_service_code, 10, ' ');
        }
    }
}