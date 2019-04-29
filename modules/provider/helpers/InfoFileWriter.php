<?php

namespace app\modules\provider\components\helpers;


use app\modules\afip\exports\AbstractExportWriter;
use app\modules\provider\components\helpers\LineWriter;

class InfoFileWriter extends AbstractExportWriter
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
            foreach ($this->data as $data) {
                $this->addLine(new LineWriter(), $data);
            }

        } catch(\Exception $ex) {
            error_log($ex->getMessage());
        }
    }
}