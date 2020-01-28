<?php

use yii\db\Migration;

/**
 * Class m200114_160537_created_at_customer_has_discount
 */
class m200122_111818_add_employee_table extends Migration
{

    public function safeUp()
    {
        $this->createTable('employee', [
            'employee_id' => $this->primaryKey(),
            'name' => $this->string(),
            'lastname' => $this->string(),
            'document_type_id' => $this->integer(),
            'document_number' => $this->string(),
            'address_id' => $this->integer(),
            'phone' => $this->string(),
            'email' => $this->string(),
            'account_id' => $this->integer(),
            'tax_condition_id' => $this->integer(),
            'company_id' => $this->integer(),
            'birthdday' => $this->date()->defaultValue(null)
        ]);

        $this->addForeignKey('fk_employee_document_type_id', 'employee', 'document_type_id', 'document_type', 'document_type_id');
        $this->addForeignKey('fk_employee_address_id', 'employee', 'address_id', 'address', 'address_id');
        $this->addForeignKey('fk_employee_tax_condition_id', 'employee', 'tax_condition_id', 'tax_condition', 'tax_condition_id');
        $this->addForeignKey('fk_employee_tax_company_id', 'employee', 'company_id', 'company', 'company_id');

        $this->createTable('employee_bill', [
            'employee_bill_id' => $this->primaryKey(),
            'date' => $this->date()->defaultValue(null),
            'number' => $this->string(),
            'net' => $this->double(),
            'taxes' => $this->double(),
            'total' => $this->double(),
            'employee_id' => $this->integer(),
            'description' => $this->text(),
            'timestamp' => $this->integer(),
            'balance' => $this->double(),
            'bill_type_id' => $this->integer(),
            'status' => "ENUM('draft', 'closed')",
            'company_id' => $this->integer(),
            'partner_distribution_model_id' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'creator_user_id' => $this->integer(),
            'updater_user_id' => $this->integer()
        ]);

        $this->addForeignKey('fk_employee_bill_employee_id', 'employee_bill', 'employee_id', 'employee', 'employee_id');
        $this->addForeignKey('fk_employee_bill_bill_type_id', 'employee_bill', 'bill_type_id', 'bill_type', 'bill_type_id');
        $this->addForeignKey('fk_employee_bill_partner_distribution_model_id', 'employee_bill', 'partner_distribution_model_id' , 'partner_distribution_model', 'partner_distribution_model_id');

        $this->createTable('employee_bill_item', [
            'employee_bill_item_id' => $this->primaryKey(),
            'employee_bill_id' => $this->integer(),
            'account_id' => $this->integer(),
            'description' => $this->text(),
            'amount' => $this->double(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'creator_user_id' => $this->integer(),
            'updater_user_id' => $this->integer()
        ]);

        $this->addForeignKey('fk_employee_bill_item_employee_bill_id', 'employee_bill_item', 'employee_bill_id', 'employee_bill', 'employee_bill_id');
        $this->addForeignKey('fk_employee_bill_item_account_id', 'employee_bill_item', 'account', 'account_id');

        $this->createTable('employee_bill_has_tax_rate', [
            'employee_bill_has_tax_rate_id' => $this->primaryKey(),
            'employee_bill_id' => $this->integer(),
            'tax_rate_id' => $this->integer(),
            'amount' => $this->double(),
            'net' => $this->double()
        ]);

        $this->addForeignKey('fk_employee_bill_has_tax_rate_employee_bill_id', 'employee_bill_has_tax_rate', 'employee_bill_id', 'employee_bill', 'employee_bill_id');
        $this->addForeignKey('fk_employee_bill_has_tax_rate_tax_rate_id', 'employee_bill_has_tax_rate', 'tax_rate_id', 'tax_rate', 'tax_rate_id');

        $this->createTable('employee_payment', [
            'employee_payment_id' => $this->primaryKey(),
            'employee_id' => $this->integer(),
            'date' => $this->date()->defaultValue(null),
            'amount' => $this->double(),
            'description' => $this->text(),
            'timestamp' => $this->integer(),
            'balance' => $this->double(),
            'company_id' => $this->integer(),
            'status' => "ENUM('created', 'closed')",
            'partner_distribution_model_id' => $this->integer()
        ]);

        $this->addForeignKey('fk_employee_payment_employee_id', 'employee_payment', 'employee_id', 'employee', 'employee_id');
        $this->addForeignKey('fk_employee_payment_company_id', 'employee_payment', 'company_id', 'company', 'company_id');

        $this->createTable('employee_payment_item', [
            'employee_payment_item_id' => $this->primaryKey(),
            'employee_payment_id' => $this->integer(),
            'description' => $this->text(),
            'number' => $this->string(),
            'amount' => $this->double(),
            'payment_method_id' => $this->integer(),
            'paycheck_id' => $this->integer(),
            'money_box_account_id' => $this->integer()
        ]);

        $this->addForeignKey('fk_employee_payment_item_employee_payment_id', 'employee_payment_item', 'employee_payment_id', 'employee_payment', 'employee_payment_id');
        $this->addForeignKey('fk_employee_payment_item_payment_method_id', 'employee_payment_item', 'payment_method_id', 'payment_method', 'payment_method_id');
        $this->addForeignKey('fk_employee_item_paycheck_id', 'employee_payment_item', 'paycheck_id', 'paycheck', 'paycheck_id');
        $this->addForeignKey('fk_employee_payment_item_money_box_account_id', 'employee_payment_item', 'money_box_account_id', 'money_box_account', 'money_box_account_id');

        $this->createTable('employee_bill_has_employee_payment', [
            'employee_bill_has_employee_payment_id' => $this->primaryKey(),
            'employee_bill_id' => $this->integer(),
            'employee_payment_id' => $this->integer(),
            'amount' => $this->double()
        ]);

        $this->addForeignKey('fk_employee_bill_has_employee_payment_employee_bill_id', 'employee_bill_has_employee_payment', 'employee_bill_id', 'employee_bill', 'employee_bill_id');
        $this->addForeignKey('fk_employee_bill_has_employee_payment_employee_payment_id', 'employee_bill_has_employee_payment', 'employee_payment_id', 'employee_payment', 'employee_payment_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('employee_bill_has_employee_payment');
        $this->dropTable('employee_payment_item');
        $this->dropTable('employee_payment');
        $this->dropTable('employee_bill_has_tax_rate');
        $this->dropTable('employee_bill_item');
        $this->dropTable('employee_bill');
        $this->dropTable('employee');
    }
}
