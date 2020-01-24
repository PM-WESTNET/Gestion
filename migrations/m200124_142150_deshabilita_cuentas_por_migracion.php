<?php

use yii\db\Migration;

/**
 * Class m200124_142150_deshabilita_cuentas_por_migracion
 */
class m200124_142150_deshabilita_cuentas_por_migracion extends Migration
{
    private $accounts_to_disable = ['5.4.1', '5.4.3', '5.4.4', '2.10' ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        foreach ($this->accounts_to_disable as $code) {
            $account = \app\modules\accounting\models\Account::findOne(['code' => $code]);

            if ($account) {
                $account->updateAttributes(['status' => \app\modules\accounting\models\Account::DISABLED_STATUS]);
            }
        }

        $migration_account = \app\modules\accounting\models\Account::findOne(['code' => '7']);

        if ($migration_account) {
            $child_accounts = \app\modules\accounting\models\Account::find()
                ->andWhere(['>=', 'lft', $migration_account->lft])
                ->andWhere(['<', 'rgt', $migration_account->rgt])
                ->all();

            foreach ($child_accounts as $account) {
                if ($account->code !== '7.71'){
                    $account->updateAttributes(['status' => \app\modules\accounting\models\Account::DISABLED_STATUS]);
                }
            }

            $migration_account->updateAttributes(['status' => \app\modules\accounting\models\Account::DISABLED_STATUS]);

        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        foreach ($this->accounts_to_disable as $code) {
            $account = \app\modules\accounting\models\Account::findOne(['code' => $code]);

            if ($account) {
                $account->updateAttributes(['status' => \app\modules\accounting\models\Account::ENABLED_STATUS]);
            }
        }

        $migration_account = \app\modules\accounting\models\Account::findOne(['code' => '7']);

        if ($migration_account) {
            if ($migration_account) {
                $child_accounts = \app\modules\accounting\models\Account::find()
                    ->andWhere(['>=', 'lft', $migration_account->lft])
                    ->andWhere(['<', 'rgt', $migration_account->rgt])
                    ->all();

                foreach ($child_accounts as $account) {
                    $account->updateAttributes(['status' => \app\modules\accounting\models\Account::ENABLED_STATUS]);
                }

                $migration_account->updateAttributes(['status' => \app\modules\accounting\models\Account::ENABLED_STATUS]);
            }
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200124_142150_deshabilita_cuentas_por_migracion cannot be reverted.\n";

        return false;
    }
    */
}
