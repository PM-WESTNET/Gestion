<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 10/05/19
 * Time: 18:00
 */

namespace app\modules\automaticdebit\components;


class BancoFrances implements BankInterface
{

    public $companyConfig;
    public $debitAutomatics;
    public $processTimestamp;
    public $paymentsData;
    private $fileName;

    /**
     *  Debe exportar el archivo para debito directo
     * @param $company_id
     * @return mixed
     */
    public function export($company_id)
    {

    }

    /**
     * Debe importar el archivo con los pagos y crear los pagos correspondientes
     * @return mixed
     */
    public function import()
    {

    }

    private function addHeader($resource) {
        $register_code = '4110';
        $companyId = $this->companyConfig->company_identification;
        $createDate = date('Ymd');
        $processDate = date('Ymd', $this->processTimestamp);
        $bank = '0017';
        $branch = $this->companyConfig->branch;
        $dc= $this->companyConfig->control_digit;
        $divisa = 'ARS';
        $devolucion = '0';
        $file= $this->fileName.'.txt';
        $ord = str_pad($this->companyConfig->company->name, 36, ' ');
        $tipoCBU= '20';
        $libre = str_pad(' ', 141, ' ');

        $line = $register_code.$companyId.$createDate.$processDate.$bank.$branch.$dc.$divisa.$devolucion.$file.$ord.$tipoCBU.$libre;

        fwrite($resource, $line.PHP_EOL);

    }

    private
}