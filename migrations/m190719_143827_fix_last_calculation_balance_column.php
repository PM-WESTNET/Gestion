<?php

use yii\db\Migration;

/**
 * Class m190719_143827_fix_last_calculation_balance_column
 */
class m190719_143827_fix_last_calculation_balance_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $customers = \app\modules\sale\models\Customer::find()->andWhere(['IS NOT','last_calculation_current_account_balance', null])->all();
        $this->addColumn('customer', 'last_balance', 'INT NULL');

        foreach ($customers as $customer) {
            $time= strtotime($customer->last_calculation_current_account_balance);
            $customer->updateAttributes(['last_balance' => $time]);
        }

        $this->dropColumn('customer', 'last_calculation_current_account_balance');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $customers = \app\modules\sale\models\Customer::find()->andWhere(['IS NOT','last_balance', null])->all();
        $this->addColumn('customer', 'last_calculation_current_account_balance', $this->string());

        foreach ($customers as $customer) {
            $time= date('Y-m-d', $customer->last_calculation_current_account_balance);
            $customer->updateAttributes(['last_calculation_current_account_balance' => $time]);
        }

        $this->dropColumn('customer', 'last_balance');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190719_143827_fix_last_calculation_balance_column cannot be reverted.\n";

        return false;
    }
    */
}
