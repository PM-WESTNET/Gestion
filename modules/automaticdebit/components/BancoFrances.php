<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 10/05/19
 * Time: 18:00
 */

namespace app\modules\automaticdebit\components;


use app\modules\automaticdebit\models\AutomaticDebit;
use app\modules\automaticdebit\models\BankCompanyConfig;
use app\modules\automaticdebit\models\BillHasExportToDebit;
use app\modules\automaticdebit\models\DebitDirectImport;
use app\modules\automaticdebit\models\DebitDirectImportHasPayment;
use app\modules\automaticdebit\models\DirectDebitExport;
use app\modules\checkout\models\Payment;
use app\modules\checkout\models\PaymentMethod;
use app\modules\sale\models\Bill;
use app\modules\sale\models\Customer;
use yii\base\InvalidConfigException;

class BancoFrances implements BankInterface
{

    public $companyConfig;
    public $debitAutomatics;
    public $processTimestamp;
    public $paymentsData;
    public $periodFrom;
    public $periodTo;
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

        $filename = time().uniqid();
        $file = \Yii::getAlias('@app').'/web/direct_debit/'.$filename;

        $resource = fopen($file.'.txt', 'w');

        if ($resource === false) {
            return false;
        }

        $export->file = $file.'.txt';
        $this->fileName = $filename;

        $export->save();

        $debits= $this->getDebits($export->bank_id, $export->company_id);

        $resource = $this->addHeader($resource);
        $totalRegister = 1;
        $totalOperations = 0;
        $totalAmount = 0;
        foreach ($debits as $debit) {
            $bills = $this->getCustomerBills($debit->customer_id, $this->periodFrom, $this->periodTo);

            foreach ($bills as $bill) {
                $resource = $this->addFirstLine($resource, $debit, $bill);
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
        while ($line = fgets($resource)){
            $code_line = substr($line,0,4);

            switch($code_line) {
                case '4110':
                    $companyId = substr($line, 4,2);

                    $companyConfig = BankCompanyConfig::findOne(['company_identification' => $companyId]);

                    if (empty($companyConfig)) {
                        throw new InvalidConfigException('Company not configured');
                    }

                    $process_timestamp = substr($line, 17, 8);
                    break;
                case '4210':
                    $beneficiary_id = substr($line, 11, 22);

                    $import1 = substr($line, 55, 13);
                    $import2 = substr($line, 68, 2);

                    $code = substr($line, 73, 2);

                    if  ($code === '00') {
                        $payments[] = [
                            'customer_code' => ltrim($beneficiary_id, '0'),
                            'amount' => (double) ($import1.'.'.$import2),
                            'date' =>  $this->restoreDate($proccess_timestamp),
                            'cbu' => substr($line, 33,22),
                        ];
                    }
                    break;
            }
        }
        $import->process_timestamp = strtotime($this->restoreDate($process_timestamp));


        $import->save();

        \Yii::info(print_r($import->getErrors(),1));

        $payment_method = PaymentMethod::findOne(['name' => 'Débito Directo']);

        foreach ($payments as $payment) {
            $customer = Customer::findOne(['code' => $payment['customer_code']]);

            if (!empty($customer)) {
               $p = new Payment([
                   'customer_id' => $customer->customer_id,
                   'amont' => $payment['amount'],
                   'partner_distribution_model_id' => $companyConfig->company->partner_distribution_model_id,
                   'company_id' => $companyConfig->company_id
               ]);

               if ($p->save()) {
                    $payment_item = [
                        'amount'=> $payment['amount'],
                        'description'=> 'Debito Directo cta ' . $payment['cbu'],
                        'payment_method_id'=> $payment_method->payment_method_id,
                        'money_box_account_id'=> $import->money_box_account_id,
                        'payment_id' => $p->payment_id
                    ];

                    $p->addItem($payment_item);

                    $ddihp= new DebitDirectImportHasPayment([
                        'debit_direct_import_id' => $import->debit_direct_import_id,
                        'payment_id' => $p->payment_id
                    ]);

                    $ddihp->save();
               }
            }
        }
    }

    private function addHeader($resource)
    {
        $register_code = '4110';
        $companyId = $this->companyConfig->company_identification;
        $createDate = date('Ymd');
        $processDate = date('Ymd', $this->processTimestamp);
        $bank = '0017';
        $branch = $this->companyConfig->branch;
        $dc= $this->companyConfig->control_digit;
        $divisa = 'ARS';
        $devolucion = '0';
        $file= $this->fileName;
        $ord = str_pad($this->companyConfig->company->name, 36, ' ');
        $tipoCBU= '20';
        $libre = str_pad(' ', 141, ' ');

        $line = $register_code.$companyId.$createDate.$processDate.$bank.$branch.$dc.$divisa.$devolucion.$file.$ord.$tipoCBU.$libre;

        fwrite($resource, $line.PHP_EOL);

        return $resource;

    }

    /**
     * @param $resource
     * @param AutomaticDebit $beneficiary
     * @param Bill $bill
     */
    private function addFirstLine($resource, $beneficiary, $bill)
    {
        $register_code = '4210';
        $companyId = $this->companyConfig->company_identification;
        $free1= str_pad(' ', 2, ' ');
        $beneficiary_number = $beneficiary->beneficiario_number;
        $cbu = $beneficiary->cbu;

        $intamount = floor($bill->total);

        $import1 = str_pad('0', 13, $intamount);
        $import2 = round(($bill->total - $intamount), 2) * 100;
        $code_dev = str_pad(' ', 6, ' ');
        $ref = str_pad(' ', 22, ' ');
        $fecha = date('Ymd', $this->processTimestamp);
        $free2 = str_pad(' ', 2, ' ');
        $bill_number = str_pad('0', 15, $bill->number);
        $status_dev = str_pad(' ', 1, ' ');
        $descr_dev = str_pad(' ', 40, ' ');
        $free3 = str_pad(' ', 86, ' ');

        $line = $register_code.$companyId.$free1.$beneficiary_number.$cbu.$import1.$import2.$code_dev.$ref.$fecha.$free2.$bill_number.$status_dev.$descr_dev.$free3;

        fwrite($resource, $line.PHP_EOL);

        return $resource;

    }

    private function addSecondLine ($resource, $beneficiary) {

        $register_code = '4220';
        $companyId = $this->companyConfig->company_identification;
        $free1= str_pad(' ', 2, ' ');
        $beneficiary_number = $beneficiary->beneficiario_number;
        $beneficiary_name = $beneficiary->customer->fullName;

        $dom1 = str_pad(' ', 36, ' ');
        $dom2 = str_pad(' ', 36, ' ');
        $free2 = str_pad(' ', 109, ' ');

        $line = $register_code.$companyId.$free1.$beneficiary_number.$beneficiary_name.$dom1.$dom2.$free2;

        fwrite($resource, $line.PHP_EOL);

        return $resource;
    }

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

        fwrite($resource, $line.PHP_EOL);

        return $resource;
    }

    private function addConceptLine($resource, $beneficiary)
    {
        $register_code = '4240';
        $companyId = $this->companyConfig->company_identification;
        $free1= str_pad(' ', 2, ' ');
        $beneficiary_number = $beneficiary->beneficiario_number;
        $concept = $beneficiary->customer->company->name;
        $free2 = str_pad(' ', 177, ' ');

        $line = $register_code.$companyId.$free1.$beneficiary_number.$concept.$free2;

        fwrite($resource, $line.PHP_EOL);

        return $resource;
    }

    private function addFooterLine ($resource, $total, $countOp, $countTotal)
    {
        $register_code = '4910';
        $companyId = $this->companyConfig->company_identification;

        $totalint = floor($total);
        $import1 = str_pad('0', 13, $totalint);
        $import2 = ($total - $totalint) * 100;
        $free =  str_pad(' ', 208, ' ');

        $line = $register_code.$companyId.$import1.$import2.$countOp.$countTotal.$free;

        fwrite($resource, $line.PHP_EOL);

        return $resource;
    }


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
            ->andWhere(['bt.class' => \app\modules\sale\models\bills\Bill::class])
            ->all();

        return $bills;
    }

    private function getDebits($bank_id, $company_id) {
        $debits = AutomaticDebit::find()
            ->innerJoin('customer c', 'c.customer_id=automatic_debit.customer_id')
            ->andWhere([
                'bank_id' => $bank_id,
                'c.company_id' => $company_id,
                'automatic_debit.status' => AutomaticDebit::ENABLED_STATUS
            ])->all();

        return $debits;
    }

    private function restoreDate($date)
    {
        $restore = substr($date,0,4) .'-'. substr($date, 4,2).'-'.substr($date,6);

        return $restore;
    }
}