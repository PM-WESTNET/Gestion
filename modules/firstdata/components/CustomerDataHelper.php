<?php

namespace app\modules\firstdata\components;


class CustomerDataHelper {


    /**
     * Devuevle el nro de tarjeta de credito del cliente recibido.
     * TODO: Se debe realizar la integracion con la api a desarrollar por WN. Por el momento se usa un mock
     */
    public static function getCustomerCreditCard($customer)
    {
        $mock = new CustomerDataMock();
        $data = $mock->getData();

        return $data['credit_card'];
    }
}

// Mock temporal hasta que se desarolle la api correspondiente
class CustomerDataMock {

    public function getData() {

        return [
            'credit_card' => "1234567891234567"
        ];
    }

}