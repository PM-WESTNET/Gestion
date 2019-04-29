<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 23/06/15
 * Time: 13:14
 */

namespace app\modules\invoice\components\einvoice;


abstract class InvoiceDTO
{
    public $errors = array();
    public $billType;
    public $pointOfSale;
    public $number;
    public $numberFrom;
    public $numberTo;
    public $date;
    public $documentType;
    public $document;
    public $concept;

    public $subTotalPrice;
    public $finalPrice;
    public $taxesPrices;

    public $taxedPrice;
    public $noTaxedPrice;
    public $exemptPrice;
    public $tributesPrices;

    public $serviceDateFrom;
    public $serviceDateTo;

    public $expirationDate;
    public $currency;
    public $currencyQuotation;
    public $observation;

    public $items;
    public $taxes;
    public $tributes;
    public $associatedItems;

    public abstract function getRequest();
    public abstract function validate();

    protected function addError($code, $message)
    {
        $this->errors[] = [
            'code'      => $code,
            'message'   => $message
        ];
    }
}
