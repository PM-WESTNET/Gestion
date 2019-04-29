<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 23/06/15
 * Time: 13:14
 */

namespace app\modules\invoice\components\einvoice\afip\mtxca;


use app\modules\invoice\components\einvoice\InvoiceDTO;
use app\modules\sale\modules\invoice\components\BillDTO;

class InvoiceMtxca extends InvoiceDTO
{
    /** @var  BillDTO */
    private $bill;

    public function __construct($object)
    {
        $this->bill                 = $object;

        $pointOfSale                = $this->bill->pointOfSale;
        $this->billType             = $this->bill->billType->code;      // El tipo solo trae la letra, no sirve - Pasar id the afip
        $this->pointOfSale          = $pointOfSale;
        $this->number               = $this->bill->number;    // No se cu
        $this->date                 = (new \DateTime($this->bill->date))->format("Y-m-d");;
        $this->documentType         = $this->bill->customer->documentType->code;
        $this->document             = $this->bill->customer->document_number;

        // Si tiene impuesto se pone en valores gravados
        // 
        // 
        //IMPORTANTE: calculateAmount devuelve el valor sin impuestos
        //
        //
        if( $this->bill->calculateTaxes() > 0 ) {
            $this->taxedPrice       = $this->bill->calculateAmount();
            $this->noTaxedPrice     = 0;
        } else {
            $this->taxedPrice       = 0;
            $this->noTaxedPrice     = $this->bill->calculateAmount();
        }

        $this->exemptPrice          = 0;
        $this->subTotalPrice        = $this->bill->calculateTotal();
        $this->tributesPrices       = 0;
        $this->finalPrice           = $this->bill->calculateTotal();
        $this->currency             = 'PES';//$this->bill->currency->code;
        $this->currencyQuotation    = 1;
        $this->observation          = "";

        // Verifico que lo facturado sea todo productos o servicios
        $class = 0;
        foreach ($this->bill->billDetails as $detail) {
            if( $detail->product->type == 'service' && ($class & 2) == 0 ) {
                $class += 2;
            } elseif( $detail->product->type != 'service' && ($class & 1) == 0 ) {
                $class += 1;
            }
            foreach($detail->product->taxRates as $taxRate){
                $taxCode = $taxRate->tax->code;
            }
            /**
             * CONSIDERAR QUE $detail->product PUEDE ESTAR AUSENTE
             */
            $items[] = [
                'id'                    => $detail->product->product_id,
                'descripcion'           => $detail->concept,
                'cantidad'              => $detail->qty,
                'unidadMedida'          => $detail->product->unit->code,
                'precioUnitario'        => $detail->unit_net_price,
                'importeBonificacion'   => 0,
                'codigoIva'             => $taxCode,
                'importe'               => $detail->line_subtotal,
                //Obtenemos el valor del impuesto sobre el producto
                'iva'                   => $detail->product->calculateTaxAmount('iva', $detail->line_subtotal)
            ];
        }

        $this->items = $items;

        $this->concept = $class;

        $this->tributes = array();

        $this->iva = [
            [
                'id' => $taxCode,
                'importeIva' =>  $this->bill->calculateTaxes()
            ]
        ];

    }

    public function getRequest()
    {
        $arrayOtrosTributos = array();
        foreach($this->tributes as $key => $value) {
            $arrayOtrosTributos[] = [
                'otroTributo' => [
                    'codigo'        => $value['id'],
                    'descripcion'   => $value['desc'],
                    'baseImponible' => $value['alicuota'],
                    'importe'       => $value['importe']
                ]
            ];
        }

        $iva = array();
        foreach($this->iva as $key => $value) {
            $iva['subtotalIVA'] = [
                'codigo'      => $value['id'],
                'importe'     => $value['importeIva'],
            ];
        }

        $items = array();
        foreach($this->items as $key => $value) {
            $items[] = [
                'unidadesMtx'           => ($value['unidadMedida']==97||$value['unidadMedida']==97 ? "1" : "1" ),
                'codigoMtx'             => ($value['unidadMedida']==97||$value['unidadMedida']==97 ? "999999999999" : "7790001001139" ),
                'codigo'                => "P".$value['id'],
                'descripcion'           => $value['descripcion'],
                'cantidad'              => $value['cantidad'],
                'codigoUnidadMedida'    => $value['unidadMedida'],
                'precioUnitario'        => $value['precioUnitario'],
                'importeBonificacion'   => $value['importeBonificacion'],
                'codigoCondicionIVA'    => $value['codigoIva'],
                'importeItem'           => $value['importe'],
                'importeIVA'            => $value['iva']
            ];
            //$items['item'] = $item;
        }

        $rta = [
            'comprobanteCAERequest' => [
                'codigoTipoComprobante'     => $this->billType,
                'numeroPuntoVenta'          => $this->pointOfSale,
                'numeroComprobante'         => $this->number,
                'fechaEmision'              => $this->date,
                'codigoTipoDocumento'       => $this->documentType,
                'numeroDocumento'           => $this->document,
                'importeGravado'            => $this->taxedPrice,
                'importeNoGravado'          => $this->noTaxedPrice,
                'importeExento'             => $this->exemptPrice,
                'importeSubtotal'           => $this->subTotalPrice,
                //'importeOtrosTributos'      => $this->tributesPrices,
                'importeTotal'              => $this->finalPrice,
                'codigoMoneda'              => $this->currency,
                'cotizacionMoneda'          => $this->currencyQuotation,
                'observaciones'             => $this->observation,
                'codigoConcepto'            => $this->concept,
                'arrayItems'                => $items,
                'arraySubtotalesIVA'        => $iva
            ]
        ];
        if(!empty($arrayOtrosTributos)) {
            $rta['arrayOtrosTributos'] = $arrayOtrosTributos;
        }
        return $rta;
    }

    public function validate()
    {
        // TODO: Implement validate() method.
    }
}