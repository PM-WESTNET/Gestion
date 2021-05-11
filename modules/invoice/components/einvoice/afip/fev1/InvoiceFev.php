<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 23/06/15
 * Time: 13:14
 */

namespace app\modules\invoice\components\einvoice\afip\fev1;


use app\modules\config\models\Config;
use app\modules\invoice\components\einvoice\InvoiceDTO;
use app\modules\sale\models\Discount;
use app\modules\sale\models\TaxRate;
use Yii;

/**
 * Class InvoiceFev
 * Implementacion de InvoiceDTO para el webservice Fev1
 *
 * @package app\modules\invoice\components\einvoice\afip\fev1
 */
class InvoiceFev extends InvoiceDTO
{
    
    /** @var  BillDTO */
    private $bill;

    public function __construct($object)
    {
        $this->bill = $object;
    }

    private function load()
    {
        $this->billType             = $this->bill->billType->code;      // El tipo solo trae la letra, no sirve - Pasar id the afip
        $this->pointOfSale          = $this->bill->getPointOfSale()->number;
        $this->number               = $this->bill->number;
        $this->date                 = (new \DateTime($this->bill->date))->format("Ymd");
        $this->documentType         = (!$this->bill->customer->document_number ? 99 : $this->bill->customer->documentType->code);
        $this->document             = str_replace("-","", $this->bill->customer->document_number);
        $this->numberFrom           = $this->bill->number;
        $this->numberTo             = $this->bill->number;

        // Si tiene impuesto se pone en valores gravados
        // 
        // 
        //IMPORTANTE: calculateAmount devuelve el valor sin impuestos
        //

        $this->taxes = array();
        $this->noTaxedPrice = 0;

        $this->taxedPrice = 0;
        $this->taxesPrices = 0;

        // Si el cliente esta exento, los importes se cargan en exento y los impuestos no se informan.
        if ($this->bill->customer->taxCondition->exempt) {
            $this->exemptPrice = round($this->bill->calculateTotal(),2);
            $this->noTaxedPrice = 0;
            $this->taxes['exento'] = [
                'Id' => 3,
                'Importe' => 0,
                'BaseImp' => 0
            ];
        }

        $this->subTotalPrice        = round($this->bill->calculateTotal(),2); // NO se usa
        $this->tributesPrices       = 0;
        $this->finalPrice           = round($this->bill->calculateTotal(),2);
        $this->currency             = 'PES';
        $this->currencyQuotation    = 1;
        $this->observation          = "";

        // Verifico que lo facturado sea todo productos o servicios
        $class = 0;

        if (!$this->bill->customer->taxCondition->exempt) {
            $billDetails = $this->bill->billDetails;
            // Determino los descuentos
            $descuentoTotal = 0;
            $cantDescuentos = 0;

            foreach ($this->bill->getTaxesApplied() as $aTax ) {
                if($aTax['amount']>0) {
                    $tax = TaxRate::findOne(['tax_id'=>$aTax['tax_id']]);
                    if(array_key_exists($tax->code, $this->taxes)===false) {
                        if($tax->code != null) {
                            $this->taxes[$tax->code] = [
                                'Id' => $tax->code,
                                'Importe' => round((float)(array_key_exists($tax->code, $this->taxes) !== false ? $this->taxes[$tax->code]['Importe'] : 0) + ($aTax['amount']),2),
                                'BaseImp' => round((float)(array_key_exists($tax->code, $this->taxes) !== false ? $this->taxes[$tax->code]['BaseImp']  : 0) + ($aTax['base']),2)
                            ];
                            $this->taxedPrice += $aTax['base'];
                            $this->taxesPrices += ($this->bill->customer->taxCondition->exempt ? 0 : $aTax['amount']);
                        }
                    }
                }
            }
            foreach ($billDetails as $detail) {
                /**
                 * CONSIDERAR QUE $detail->product PUEDE ESTAR AUSENTE
                 */
                // Si tiene un producto asociado
                if (isset($detail->product)) {
                    if ($detail->product->type == 'service' && ($class & 2) == 0) {
                        $class += 2;
                    } elseif ($detail->product->type != 'service' && ($class & 1) == 0) {
                        $class += 1;
                    }
                }
            }
        }

        $this->concept          = ($class==0 ? 1 : $class);
        $this->tributes         = array();
        $this->associatedItems  = array();
        $this->optionals        = array();
    }

    /*
     * Retorna el array del request que se envia al web service de la afip
     *
     */
    public function getRequest()
    {
        $this->load();

        $CbtesAsoc = array();
        foreach($this->associatedItems as $key => $value) {
            $CbtesAsoc[] = [
                'CbteAsoc' => [
                    'Tipo'      => $value['tipo'],
                    'PtoVta'    => $value['puntoDeVenta'],
                    'Nro'       => $value['numero']
                ]
            ];
        }
        $Tributos = array();
        $this->importeTributos = 0;
        foreach($this->tributes as $key => $value) {
            $Tributos[] = [
                'Tributo' => [
                    'Id'      => $value['id'],
                    'Desc'    => $value['desc'],
                    'Alic'    => $value['alicuota'],
                    'Importe' => $value['importe']
                ]
            ];
            $this->tributesPrices += $value['importe'];
        }

        $Opcionales = array();
        foreach($this->optionals as $key => $value) {
            $Opcionales[] = [
                'Opcional' => [
                    'Id'      => $value['id'],
                    'Valor'   => $value['valor'],
                ]
            ];
        }

        $Iva = array();
        foreach($this->taxes as $key => $value) {
            $Iva['AlicIva'][] = $value;
        }

        $rta =  [
            'FeCAEReq' => [
                'FeCabReq' => [
                    'CantReg' => 1,
                    'PtoVta'  => $this->pointOfSale,
                    'CbteTipo'=> $this->billType
                ],
                'FeDetReq' => [
                    'FECAEDetRequest' => [
                        'Concepto'      => $this->concept,
                        'DocTipo'       => $this->documentType,
                        'DocNro'        => (string)$this->document,
                        'CbteDesde'     => $this->numberFrom,
                        'CbteHasta'     => $this->numberTo,
                        'CbteFch'       => $this->date,
                        'ImpTotal'      => $this->finalPrice,
                        'ImpTotConc'    => $this->noTaxedPrice,
                        'ImpNeto'       => $this->taxedPrice,
                        'ImpOpEx'       => $this->exemptPrice ? $this->exemptPrice : 0,
                        'ImpTrib'       => $this->tributesPrices,
                        'ImpIVA'        => $this->taxesPrices,
                        'FchVtoPago'    => $this->expirationDate ? $this->expirationDate : 0,
                        'MonId'         => $this->currency,
                        'MonCotiz'      => $this->currencyQuotation,
                    ]
                ]
            ]
        ];

        if ($this->taxesPrices!=0) {
            $rta['FeCAEReq']['FeDetReq']['FECAEDetRequest']['Iva'] = $Iva;
        }

        if( !empty($this->serviceDateFrom) ) {
            $rta['FeCAEReq']['FeDetReq']['FECAEDetRequest']['FchServDesde'] = (new \DateTime($this->serviceDateFrom))->format("Ymd");
        }

        if( !empty($this->fechaServicioHasta) ) {
            $rta['FeCAEReq']['FeDetReq']['FECAEDetRequest']['FchServHasta'] = (new \DateTime($this->serviceDateFrom))->format("Ymd");
        }

        if(!empty($Tributos)) {
            $rta['Tributos'] = $Tributos;
        }
        if(!empty($Opcionales)) {
            $rta['Opcionales'] = $Opcionales;
        }
        if(!empty($CbtesAsoc)) {
            $rta['CbtesAsoc'] = $CbtesAsoc;
        }

	//if( in_array($rta['FeCAEReq']['FeDetReq']['CbteTipo'], ['3','8'] )){ 
	if( $rta['FeCAEReq']['FeDetReq']['CbteTipo'] = 3){
	    $rta['FeCAEReq']['FeDetReq']['FECAEDetRequest']['PeriodoAsoc']['FchDesde'] ='20210401';
            $rta['FeCAEReq']['FeDetReq']['FECAEDetRequest']['PeriodoAsoc']['FchHasta'] ='20210430'; 
        }
	// Nota
	if( $rta['FeCAEReq']['FeDetReq']['CbteTipo'] = 8){
            $rta['FeCAEReq']['FeDetReq']['FECAEDetRequest']['PeriodoAsoc']['FchDesde'] ='20210401';
            $rta['FeCAEReq']['FeDetReq']['FECAEDetRequest']['PeriodoAsoc']['FchHasta'] ='20210430';
        }
	// echo'<pre>';print_r( $rta );die;
        return $rta;
    }

    /**
     *  Valida si se puede cargar el dto o si faltan datos.
     */
    public function validate()
    {
        try{
            if( !empty($this->bill)) {

                if (empty($this->bill->billType)) {
                    $this->addError(0, Yii::t('afip', 'No Bill Type.'));
                }
                if (!$this->bill->point_of_sale_id) {
                    $this->addError(0, Yii::t('afip', 'No Point Of Sale.'));
                }
                if (empty($this->bill->customer->documentType)) {
                    $this->addError(0, Yii::t('afip', 'No Document Type in Customer.'));
                }
                //if (empty($this->bill->customer->document_number)) {
                //    $this->addError(0, Yii::t('afip', 'No Document Number in Customer.'));
                //}

            } else {
                $this->addError(0, Yii::t('afip', 'The Bill is Null.'));
            }

        } catch(\Exception $ex){
            $this->addError($ex->getCode(), $ex->getMessage());
        }
        return count($this->errors)==0;

    }
}
