<?php

namespace app\modules\sale;

class SaleModule extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\sale\controllers';

    public function init()
    {
        parent::init();

        $this->modules = [
            'invoice' => [
                'class' => 'app\modules\sale\modules\invoice\InvoiceModule',
            ],
            'api' => [
                'class' => 'app\modules\sale\modules\api\APIModule',
            ],
        ];

        $this->components = [
            'stock' => [
                'class' => '\app\modules\sale\components\StockExpert'
            ]
        ];

        $this->modules = [
            'contract' => [
                'class' => 'app\modules\sale\modules\contract\ContractModule',
            ],
        ];
    }
}
