<?php

use yii\db\Migration;

/**
 * Class m191001_211522_fix_item_resume
 */
class m191001_211522_fix_item_resume extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $resumes = \app\modules\accounting\models\Resume::find()->all();

        foreach ($resumes as $resume) {
            $items = $resume->resumeItems;

            foreach ($items as $item) {
                $nDebit= $item->credit;
                $nCredit = $item->debit;

                $item->updateAttributes(['debit' => abs($nDebit), 'credit' => abs($nCredit)]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191001_211522_fix_item_resume cannot be reverted.\n";

        return false;
    }
    */
}
