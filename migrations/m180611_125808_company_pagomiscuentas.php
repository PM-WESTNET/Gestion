<?php

use yii\db\Migration;

/**
 * Class m180611_125808_company_pagomiscuentas
 */
class m180611_125808_company_pagomiscuentas extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE company ADD COLUMN pagomiscuentas_code INT NULL;');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180611_125808_company_pagomiscuentas cannot be reverted.\n";

        return false;
    }
}
