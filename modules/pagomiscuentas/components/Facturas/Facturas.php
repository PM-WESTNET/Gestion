<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 7/06/18
 * Time: 15:23
 */

namespace app\modules\pagomiscuentas\components\Facturas;


use app\modules\afip\exports\AbstractExportWriter;
use app\modules\pagomiscuentas\components\Facturas\Detalle;
use app\modules\pagomiscuentas\components\Facturas\Header;
use app\modules\pagomiscuentas\components\Facturas\Trailer;

class Facturas extends AbstractExportWriter
{
    /**
     * @return mixed
     * @throws \Exception
     */
    public function parse()
    {
        if(is_null($this->data)) {
            throw new \Exception('No data to parse.');
        }

        try {
            $this->addLine(new Header(), [
                'company' => $this->data['company'],
                'date' => (new \DateTime('now'))->format('Ymd')
            ]);
            $cantidad = 0;
            $total = 0;
            foreach ($this->data['data'] as $data) {
                $this->addLine( new Detalle(), $data);
                $total += $data['importe_1_vto'];
                $cantidad++;
            }

            $this->addLine(new Trailer(), [
                'company'   => $this->data['company'],
                'date'      => (new \DateTime('now'))->format('Ymd'),
                'cantidad'  => $cantidad,
                'total'     => $total
            ]);
        } catch(\Exception $ex) {
            error_log($ex->getFile() . " - " . $ex->getLine() . " - " . $ex->getMessage());
        }
    }
}