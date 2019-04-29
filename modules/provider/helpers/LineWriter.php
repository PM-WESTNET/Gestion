<?php

namespace app\modules\provider\components\helpers;


use app\modules\afip\exports\AbstractLine;

class LineWriter extends AbstractLine
{
    /**
     * @param $providers
     * @return mixed
     */
    public function parse($providers)
    {
        $this->values = $providers;
        $this->line = sprintf('%1$s', 'Id_de_proveedor: '.$providers->provider_id.' ');
        $this->line .= sprintf('%1$s', 'Nombre: '.$providers->name.' ');
        $this->line .= sprintf('%1$s', 'Nombre_legal: '.$providers->business_name.' ');
        $this->line .= sprintf('%1$s', $providers->tax_identification ? 'Número de identificación: '.$providers->tax_identification.' ' : 'Número de identificación: Sin Número de identificación'.' ');
    }

    /**
     * @return boolean
     */
    public function validate()
    {
        return true;
    }
}