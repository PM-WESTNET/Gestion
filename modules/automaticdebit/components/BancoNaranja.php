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

class BancoNaranja implements BankInterface
{

    public $BUSSINES_NUMBER = 562596678;

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

        $filename = 'daf'.'-'.date('dm').'.'.$export->direct_debit_export_id;

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

        $this->addHeader($resource);

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

                //$bhetd->save();
            }           

        }
        
        $resource = $this->addFooterLine($resource, $totalAmount, $export, $debits);

        fclose($resource);

        return true;
    }

    public function addHeader($resource){
        $bussines_number = 'C' . $this->BUSSINES_NUMBER;
        $filler = str_pad('', '97', ' ', STR_PAD_RIGHT);
        $date_of_presentation = date('Ymd');
        $line = $bussines_number . $filler . $date_of_presentation;

        fwrite($resource, mb_convert_encoding($line.PHP_EOL, 'Windows-1252'));

        return $resource;

    }

    public function addBody($resource, $debit, $export, $totalImport){
        $data_register = 'D';
        $credit_card_number = substr($debit->cbu, 0, 16);

        $intamount = floor(round($totalImport, 2));
        $import = str_pad($intamount, 10, '0', STR_PAD_LEFT);
        $import_decimal = round(($totalImport - $intamount), 2) * 100 ;
        if ($import_decimal < 10) {
            $import_decimal = str_pad(abs($import_decimal), 2, 0, STR_PAD_LEFT);
        }

        $import .= str_pad($import_decimal, 2, '0', STR_PAD_LEFT);
        $debit_high_date = date('Ymd', $debit->created_at);
        $reference_number = str_pad($debit->customer->code, 30, ' ', STR_PAD_RIGHT);
        $due_date = date('Ym').'20';
        $number_of_quota = '01';
        $period = $export->from_date = date('Ymd');
        $filler1 = str_pad('', '6', ' ', STR_PAD_RIGHT);
        $filler2 = str_pad('', '12', ' ', STR_PAD_RIGHT);
        $filler3 = str_pad('', '8', ' ', STR_PAD_RIGHT);

        $line = $data_register .
                $credit_card_number .
                $import .
                $debit_high_date .
                $reference_number . 
                $due_date . 
                $number_of_quota .
                $period .
                $filler1 .
                $filler2 .
                $filler3;

        fwrite($resource, mb_convert_encoding($line.PHP_EOL, 'Windows-1252'));

        return $resource;

    }

    public function addFooterLine($resource, $totalAmount, $export, $debits){        
        $registry_number = 'P' . str_pad(count($debits), '6', '0', STR_PAD_LEFT);
        $filler1 = str_pad('', 8, ' ', STR_PAD_RIGHT);
        $filler2 = str_pad('', 12, ' ', STR_PAD_RIGHT);
        $filler3 = str_pad('', 8, ' ', STR_PAD_RIGHT);
        $filler4 = str_pad('', 30, ' ', STR_PAD_RIGHT);
        $filler5 = str_pad('', 8, ' ', STR_PAD_RIGHT);
        $filler6 = str_pad('', 2, ' ', STR_PAD_RIGHT);
        $filler7 = str_pad('', 8, ' ', STR_PAD_RIGHT);
        $filler8 = str_pad('', 4, ' ', STR_PAD_RIGHT);
        $filler9 = str_pad('', 8, ' ', STR_PAD_RIGHT);      

        $totalint = floor($totalAmount);
        $import1 = str_pad($totalint, 10, '0', STR_PAD_LEFT);
        $import2 = round(($totalAmount - $totalint),2) * 100;

        if ($import2 < 10) {
            $import2 = str_pad($import2, 2, '0', STR_PAD_LEFT);
        }
        $sum_of_record_amounts = $import1.$import2;

        $filler10 = str_pad('', 8, ' ', STR_PAD_RIGHT);

        $line = $registry_number .
                $filler1 . 
                $filler2 . 
                $filler3 . 
                $filler4 . 
                $filler5 . 
                $filler6 . 
                $filler7 . 
                $filler8 .
                $filler9 .
                $sum_of_record_amounts .
                $filler10;

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

    public function import($resource, $import, $fileName){

    }
}
