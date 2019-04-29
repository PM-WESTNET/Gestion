<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 1/12/16
 * Time: 10:13
 */

namespace app\modules\afip\exports\compraVenta;


use app\modules\afip\exports\AbstractExportWriter;

class CompraVentaComprobantes extends AbstractExportWriter
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
            foreach ($this->data as $data) {
                $this->addLine( ($this->is_venta ? new LineaTipo1Venta() :  new LineaTipo1Compra() ), $data);
            }
        } catch(\Exception $ex) {
            error_log($ex->getMessage());
        }
    }
}