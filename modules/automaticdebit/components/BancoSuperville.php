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
                $totalImport = abs(Payment::totalCalculationForQuery($bills[0]->customer_id));
                if ($totalImport == 0) {
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

    public function import($resource, $import, $fileName){

    }


    public function addBody($resource, $debit, $export, $totalImport){
        $current_date = date('Y-m-d');
        $type_of_newness = 'D';                                                             // Tipo de novedad
        $cuit_company = str_pad(str_replace('-','',$this->companyConfig->company->tax_identification), 11, '0', STR_PAD_LEFT); //CUIT Empresa Originante
        $sector = '001';                                                                    // Sector
        $benefit = str_pad('INTERNET',10,' ',STR_PAD_RIGHT);                                  // Prestacion
        $due_date1 = str_pad(date('dmY',strtotime($current_date . "+2 days")),8,0,STR_PAD_LEFT);                             // Fecha de Vencimiento 1
        $block_cbu_1 = str_pad(substr($debit->cbu, 0, 8),8,0,STR_PAD_LEFT);                 // Bloque 1 CBU
        $fixed_fields = '000';                                                              // Campos Fijos
        $block_cbu_2 = str_pad(substr($debit->cbu,8),14,0,STR_PAD_LEFT);                    // Bloque 2 CBU
        $customer_identification = str_pad($debit->customer_id,22,' ',STR_PAD_RIGHT);         // Identificación del cliente


        $original_debit_due_date = str_pad(date('dmY',strtotime($current_date . "+2 days")),8,0,STR_PAD_LEFT);
        $debit_reference = str_pad('FACTURA',15,' ',STR_PAD_RIGHT);

        $intamount = floor(round($totalImport, 2));
        $import = str_pad($intamount, 8, '0', STR_PAD_LEFT);
        $import_decimal = round(($totalImport - $intamount), 2) * 100 ;
        if ($import_decimal < 10) {
            $import_decimal = str_pad(abs($import_decimal), 2, 0, STR_PAD_LEFT);
        }
        $import .= str_pad($import_decimal, 2, '0', STR_PAD_LEFT);
        $debit_currency = 80;
        $due_date2 = str_pad('21'.date('mY'),8,0,STR_PAD_LEFT);
        $import2 = $import;

        $due_date3 = str_pad('28'.date('mY'),8,0,STR_PAD_LEFT);
        $import3 = $import;
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

}
