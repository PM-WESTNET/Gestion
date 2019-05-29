<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 10/05/19
 * Time: 17:56
 */

namespace app\modules\automaticdebit\components;


interface BankInterface
{


    /**
     *  Debe exportar el archivo para debito directo
     * @param $company_id
     * @return mixed
     */
    public function export($export);


    /**
     * Debe importar el archivo con los pagos y crear los pagos correspondientes
     * @return mixed
     */
    public function import();
}