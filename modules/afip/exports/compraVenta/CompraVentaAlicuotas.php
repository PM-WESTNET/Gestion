<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 1/12/16
 * Time: 10:13
 */

namespace app\modules\afip\exports\compraVenta;

use app\modules\sale\models\BillType;
use app\modules\afip\exports\AbstractExportWriter;

class CompraVentaAlicuotas extends AbstractExportWriter
{

    private $is_venta = true;

    public function __construct($data, $isVenta = true)
    {
        parent::__construct($data);
        $this->is_venta = $isVenta;
    }

    /**
     * @return mixed
     */
    public function parse()
    {
        if(is_null($this->data)) {
            throw new \Exception('No data to parse.');
        }

        try {
            $include_iva_bill_types = BillType::find()->select('code')->where("BINARY name like '% C'")->all();
            foreach ($this->data as $data) {
                $with_iva = true;
                foreach ($include_iva_bill_types as $bill_type) {
                    if ($bill_type->code == $data['tipo_comprobante']) {
                        $with_iva = false;
                    }
                }
                if($with_iva){

                    if($this->is_venta) {
                        $this->addLine( new LineaTipo2Venta(), $data);
                    } else {
                        //Es necesario generar una línea nueva de alicuota cuando el comprobante tiene diferentes tipos de iva
                        if($data['code_iva_105'] != 0) {
                            $data['tipo_de_iva'] = $data['code_iva_105'];
                            $data['impuesto_liquidado'] = $data['iva_105'];
                            $data['neto'] = $data['net_iva_105'];
                            $this->addLine(( new LineaTipo2Compra()), $data);
                        }
                        if($data['code_iva_21'] != 0) {
                            $data['tipo_de_iva'] = $data['code_iva_21'];
                            $data['impuesto_liquidado'] = $data['iva_21'];
                            $data['neto'] = $data['net_iva_21'];
                            $this->addLine(( new LineaTipo2Compra()), $data);
                        }
                        if($data['code_iva_27'] != 0) {
                            $data['tipo_de_iva'] = $data['code_iva_27'];
                            $data['impuesto_liquidado'] = $data['iva_27'];
                            $data['neto'] = $data['net_iva_27'];
                            $this->addLine(( new LineaTipo2Compra()), $data);
                        }
                        if($data['code_iva_06'] != 0) {
                            $data['tipo_de_iva'] = $data['code_iva_06'];
                            $data['impuesto_liquidado'] = $data['iva_06'];
                            $data['neto'] = $data['net_iva_06'];
                            $this->addLine(( new LineaTipo2Compra()), $data);
                        }
                        if($data['code_iva_05'] != 0) {
                            $data['tipo_de_iva'] = $data['code_iva_05'];
                            $data['impuesto_liquidado'] = $data['iva_05'];
                            $data['neto'] = $data['net_iva_05'];
                            $this->addLine(( new LineaTipo2Compra()), $data);
                        }
                        if($data['code_iva_025'] != 0) {
                            $data['tipo_de_iva'] = $data['code_iva_025'];
                            $data['impuesto_liquidado'] = $data['iva_025'];
                            $data['neto'] = $data['net_iva_025'];
                            $this->addLine(( new LineaTipo2Compra()), $data);
                        }
                    }
                }
            }
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
        }
    }
}