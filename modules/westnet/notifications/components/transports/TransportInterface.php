<?php

namespace app\modules\westnet\notifications\components\transports;

/**
 *
 * @author mmoyano
 */
interface TransportInterface {
    public function features();
    public function send($notification);
    public function export($notification);
}
