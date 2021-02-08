<?php

namespace app\modules\westnet\components\export;


use app\modules\afip\exports\AbstractLine;

class LineResult extends AbstractLine
{
    /**
     * @param $values
     * @return mixed
     * Format :ip_anterior,​ip_nueva.
     */
    public function parse($values)
    {
        $this->values = $values;
        $this->line .= ($values['old_ip'] > 0 ? long2ip($values['old_ip']) : $values['old_ip']).',';
        $this->line .= ($values['new_ip'] > 0 ? long2ip($values['new_ip']) : $values['new_ip']).',';
    }

    /**
     * @return boolean
     */
    public function validate()
    {
        return true;
    }
}