<?php

namespace app\modules\westnet\components\export;

use app\modules\afip\exports\AbstractExportWriter;
use app\modules\westnet\components\export\LineResult;

class ChangeNodeExport extends AbstractExportWriter
{

    public function __construct($data)
    {
        parent::__construct($data);
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
            $this->addLine( new LineResult(), ['new_ip' => 'ip_nueva', 'old_ip' => 'ip_anterior']);
            foreach ($this->data as $data) {
                $this->addLine( new LineResult(), $data);
            }
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
        }
    }
}