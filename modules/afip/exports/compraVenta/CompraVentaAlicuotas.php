<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 1/12/16
 * Time: 10:13
 */

namespace app\modules\afip\exports\compraVenta;


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
            $include_iva_bill_types = \app\modules\sale\models\BillType::find()->select('code')->where("BINARY name like '% C'")->all();
            foreach ($this->data as $data) {
                $with_iva = true;
                foreach ($include_iva_bill_types as $bill_type) {
                    if ($bill_type->code == $data['tipo_comprobante']) {
                        $with_iva = false;
                    }
                }
                if($with_iva){
                    $this->addLine(($this->is_venta ? new LineaTipo2Venta() : new LineaTipo2Compra()), $data);
                }
            }
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
        }
    }
}