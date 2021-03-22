<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 12/12/18
 * Time: 12:15
 */

use app\modules\sale\models\Bill;
use app\modules\sale\models\CustomerMessage;
use app\tests\fixtures\BillFixture;
use app\modules\sale\models\Company;
use app\modules\config\models\Config;
use app\modules\sale\models\Customer;
use app\modules\sale\models\Discount;
use app\modules\ticket\models\Ticket;
use app\tests\fixtures\VendorFixture;
use app\tests\fixtures\PaymentFixture;
use app\tests\fixtures\ProductFixture;
use app\modules\config\models\Category;
use app\tests\fixtures\CustomerFixture;
use app\modules\checkout\models\Payment;
use app\modules\sale\models\DocumentType;
use app\tests\fixtures\DocumentTypeFixture;
use app\tests\fixtures\TaxConditionFixture;
use app\tests\fixtures\TicketStatusFixture;
use app\modules\mobileapp\v1\models\UserApp;
use app\tests\fixtures\CustomerClassFixture;
use app\tests\fixtures\CustomerMessageFixture;
use app\modules\sale\models\ProductHasCategory;
use app\tests\fixtures\CustomerCategoryFixture;
use app\modules\sale\models\CustomerHasDiscount;
use app\modules\sale\modules\contract\models\Plan;
use app\modules\mobileapp\v1\models\UserAppActivity;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\sale\models\CustomerHasCustomerMessage;

class CustomerTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _fixtures()
    {
        return [
            'tax_condition' => [
                'class' => TaxConditionFixture::class
            ],
            'customer_class' => [
                'class' => CustomerClassFixture::class
            ],
            'customer_category' => [
                'class' => CustomerCategoryFixture::class
            ],
            'document_type' => [
                'class' => DocumentTypeFixture::class
            ],
            'bill' => [
                'class' => BillFixture::class
            ],
            'payment' => [
                'class' => PaymentFixture::class
            ],
            'customer' => [
                'class' => CustomerFixture::class
            ],
            'status' => [
                'class' => TicketStatusFixture::class
            ],
            'document_type_id' => [
                'class' => DocumentTypeFixture::class
            ],
            'customer_message' => [
                'class' => CustomerMessageFixture::class
            ],
            'product' => [
                'class' => ProductFixture::class
            ],
            'vendor' => [
                'class' => VendorFixture::class
            ]
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new Customer();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new Customer([
            'tax_condition_id' => 1,
            'name' => 'Pepe',
            'publicity_shape' => 'web',
            'document_number' => '23-29834800-4',
            'document_type_id' => 1,
            'customerClass' => 1
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new Customer();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new Customer([
            'tax_condition_id' => 1,
            'publicity_shape' => 'web',
            'document_number' => '12456789',
            'document_number' => '23-29834800-4',
            'name' => 'Pepe',
            'document_type_id' => 1,
            'customerClass' => 1,
            '_notifications_way' => [Customer::getNotificationWays()],
        ]);

        expect('Saved when full and new', $model->save())->true();
    }

    public function testGetFullName(){
        $model = new Customer([
            'name' => 'Nombre',
            'lastname' => 'Apellido',
            'tax_condition_id' => 1,
            'publicity_shape' => 'web',
            'document_number' => '23-29834800-4',
            'document_type_id' => 1,
            'customerClass' => 1,
            '_notifications_way' => [Customer::getNotificationWays()],
        ]);

        expect('Get full name', $model->getFullName())->equals('Apellido, Nombre');
    }

    public function testHasDocumentType(){
        $model = new Customer([
            'tax_condition_id' => 1,
            'publicity_shape' => 'web',
            'document_number' => '23-29834800-4',
            'name' => 'Pepe',
            'document_type_id' => 1,
            'customerClass' => 1,
            '_notifications_way' => [Customer::getNotificationWays()],
        ]);

        $document_type = DocumentType::findOne(1);
        $document_type2 = DocumentType::findOne(2);

        expect('Has document type', $model->hasDocumentType($document_type))->true();
        expect('Has not document type', $model->hasDocumentType($document_type2))->false();
    }

//    public function testFailSaveNewWhenDocumentNumberExist()
//    {
//        $model = new Customer([
//            'name' =>  'Pepe',
//            'lastname' => 'Hongo',
//            'tax_condition_id' => 2,
//            'publicity_shape' => 'web',
//            'document_number' => '35875225',
//            'document_type_id' => 2,
//            'customerClass' => 1,
//            'customerCategory' => 1,
//            '_notifications_way' => [Customer::getNotificationWays()],
//        ]);
//        $model->scenario = 'insert';
//        $model->save();
//
//        \Codeception\Util\Debug::debug(print_r($model->getErrors(), 1));
//        $model2 = new Customer([
//            'name' =>  'Pepe',
//            'lastname' => 'Hongo',
//            'tax_condition_id' => 1,
//            'publicity_shape' => 'web',
//            'document_number' => '35875225',
//            'document_type_id' => 2,
//            'customerClass' => 1,
//            'customerCategory' => 1,
//            '_notifications_way' => [Customer::getNotificationWays()],
//        ]);
//        $model2->scenario = 'insert';
//        $save = $model2->save();
//        \Codeception\Util\Debug::debug(print_r($model2->getErrors(), 1));
//        expect('Validate', $save)->false();
//    }

    public function testSuccessUpdateWhenDocumentNumberExist() {
        $model = new Customer([
            'name' =>  'Pepe',
            'lastname' => 'Hongo',
            'tax_condition_id' => 1,
            'publicity_shape' => 'web',
            'document_number' => '23-29834800-4',
            'document_type_id' => 1,
            'customerClass' => 1,
            'customerCategory' => 1,
            '_notifications_way' => [Customer::getNotificationWays()],
        ]);
        $model->scenario = 'insert';
        $model->save();

        \Codeception\Util\Debug::debug(print_r($model->getErrors(), 1));

        $model2 = new Customer([
            'name' =>  'Pepe',
            'lastname' => 'Hongo',
            'tax_condition_id' => 1,
            'publicity_shape' => 'web',
            'document_number' => '20-14978176-6',
            'document_type_id' => 1,
            'customerClass' => 1,
            'customerCategory' => 1,
            '_notifications_way' => [Customer::getNotificationWays()],
        ]);
        $model2->scenario = 'insert';
        $model2->save();

        \Codeception\Util\Debug::debug(print_r($model2->getErrors(), 1));

        $updatedModel = Customer::findOne($model2->customer_id);

        $updatedModel->document_number = '23-29834800-4';
        $updatedModel->_notifications_way = [Customer::getNotificationWays()];

        expect('Failed', $updatedModel->save())->true();
    }

    public function testValidateDocumentExistAndHaveNotDebth()
    {
        $model = new Customer([
            'document_number' => '35875227',
        ]);

        expect('Failed', $model->validateCustomer())->equals(['status' => 'no_debt']);
    }

    public function testValidateDocumentExistAndHaveDebth()
    {
        $model = new Customer([
            'document_number' => '35875226',
        ]);

        expect('Failed', $model->validateCustomer())->equals(['status' => 'debt']);
    }

    public function testValidateDocumentWhenNotExist()
    {
        $model = new Customer([
            'document_number' => '35875230',
        ]);

        expect('Failed', $model->validateCustomer())->equals(['status' => 'new']);
    }

    public function  testNeedsUpdate()
    {
        $date_three_months_ago = (new \DateTime('now'))->modify('-3 months');
        $date_today = (new \DateTime('now'));
        $model = new Customer([
            'tax_condition_id' => 1,
            'publicity_shape' => 'web',
            'document_number' => '12456789',
            'customerClass' => 1,
            '_notifications_way' => [Customer::getNotificationWays()],
            'code' => 11111,
        ]);
        $model->save();

        Config::setValue('require_update_customer_data', 1);

        expect('Needs update when last_update is null', $model->needsUpdate)->true();

        $model->updateAttributes(['last_update' => $date_three_months_ago->format('Y-m-d')]);

        expect('Needs update return true', $model->needsUpdate)->true();

        $model->updateAttributes(['last_update' => $date_today->format('Y-m-d')]);

        expect('No needs update', $model->needsUpdate)->false();

    }

    public function testHasCategoryTicket()
    {
        $category = Category::findOne(Config::getValue('cobranza_category_id'));
        $response =  Customer::hasCategoryTicket(59809, $category->category_id, true);
        $customer = Customer::findOne(['code' => 59809]);

        expect('Has no tickets', $response['customer_code'])->equals(59809);
        expect('Has no tickets', $response['has_ticket'])->equals(false);
        expect('Has no tickets', $response['ticket_status'])->equals('');

        $ticket = new Ticket([
            'title' => 'titulo',
            'user_id' => 1,
            'category_id' => $category->category_id,
            'customer_id' => $customer->customer_id,
            'status_id' => 1,
            'content' => 'contenido'
        ]);
        $ticket->save();
        Ticket::assignTicketToUser($ticket->ticket_id, 1);
        $response =  Customer::hasCategoryTicket(59809, $category->category_id, true);

        expect('Has no tickets', $response['customer_code'])->equals(59809);
        expect('Has no tickets', $response['has_ticket'])->equals(true);
        expect('Has no tickets', $response['ticket_status'])->equals('nuevo');

    }

    public function testVerifyEmails ()
    {
        $resources = fopen(Yii::getAlias('@app/tests/_data/elastics_email_test.csv'), 'r');

        if ($resources) {
            //$data = fgetcsv($resources, null, ',');

            Customer::verifyEmails($resources);
            Customer::verifyEmails($resources, 'email2');

            $customers = Customer::find()->all();
            $emails = [];

            foreach ($customers as $customer) {
                $emails[$customer->email] = $customer->email_status;
                $emails[$customer->email2] = $customer->email2_status;
            }



            $row_index = 0;
            while (($row = fgetcsv($resources, null, ',')) !== false) {

                if ($row_index > 0) {
                    \Codeception\Util\Debug::debug(print_r($row,1));
                    $email = $row[0];
                    $status = $row[1];

                    if ($emails[$email] !== strtolower($status)) {
                        expect($email . ' esperaba '. $status. ' y venia '. $emails[$email], false)->true();
                        return;
                    }
                }

                $row_index++;

            }

            expect('Fail', true)->true();
            return;
        }

        expect('Cant open file', true)->false();
    }

    public function testHasMobileAppInstalledWhenCustomerDoesntHaveAUserApp()
    {
        $model = new Customer([
            'tax_condition_id' => 1,
            'publicity_shape' => 'web',
            'document_number' => '23-29834800-4',
            'document_type_id' => 1,
            'name' => 'Cliente1',
            'customerClass' => 1,
            '_notifications_way' => [Customer::getNotificationWays()],
            'code' => 11111,
            'email' => 'customer@gmail.com',
            'status' => 'enabled'
        ]);
        $model->save();

        $user_app = new UserApp([
            'email' => 'customer@gmail.com',
            'status' => 'active',
            'document_number' => '23-29834800-4',
        ]);
        $user_app->save();

        expect('Doesnt have mobile app installed', $model->hasMobileAppInstalled())->false();
    }

    public function testHasMobileAppInstalledWhenCustomerHaveAUserApp()
    {
        $model = new Customer([
            'tax_condition_id' => 1,
            'publicity_shape' => 'web',
            'document_number' => '23-29834800-4',
            'document_type_id' => 1,
            'name' => 'Cliente1',
            'customerClass' => 1,
            '_notifications_way' => [Customer::getNotificationWays()],
            'code' => 11111,
            'email' => 'customer@gmail.com',
            'status' => 'enabled'
        ]);
        $model->save();

        $user_app = new UserApp([
            'email' => 'customer@gmail.com',
            'status' => 'active',
            'document_number' => '23-29834800-4',
        ]);
        $user_app->save();

        $user_app->addCustomer($model, true);
        UserAppActivity::createInstallationRegister($user_app->user_app_id, true);

        expect('Has mobile app installed', $model->hasMobileAppInstalled())->true();
    }

    public function testHasMobileAppInstalledWhenCustomerHaveAUserAppAndPeriodExpired()
    {
        $model = new Customer([
            'tax_condition_id' => 1,
            'publicity_shape' => 'web',
            'document_number' => '23-29834800-4',
            'document_type_id' => 1,
            'customerClass' => 1,
            'name' => 'Cliente1',
            '_notifications_way' => [Customer::getNotificationWays()],
            'code' => 11111,
            'email' => 'customer@gmail.com',
            'status' => 'enabled'
        ]);
        $model->save();

        $user_app = new UserApp([
            'email' => 'customer@gmail.com',
            'status' => 'active',
            'document_number' => '23-29834800-4',
        ]);
        $user_app->save();

        $user_app->addCustomer($model, true);
        UserAppActivity::createInstallationRegister($user_app->user_app_id, true);
        $uninstalled_period = Config::getValue('month-qty-to-declare-app-uninstalled') + 1;
        $old_last_activity = (new \DateTime('now'))->modify("-$uninstalled_period months")->getTimestamp();
        if ($user_app->activity) {
            $user_app->activity->updateAttributes(['last_activity_datetime' => $old_last_activity]);
        }

        expect('Last mobile app activity its too old to be considered installed', $model->hasMobileAppInstalled())->false();
    }

    public function testLastMobileAppUse()
    {
        $model = new Customer([
            'tax_condition_id' => 1,
            'publicity_shape' => 'web',
            'document_number' => '23-29834800-4',
            'name' => 'Cliente1',
            'document_type_id' => 1,
            'customerClass' => 1,
            '_notifications_way' => [Customer::getNotificationWays()],
            'code' => 11111,
            'email' => 'customer@gmail.com',
            'status' => 'enabled'
        ]);
        $model->save();

        $user_app = new UserApp([
            'email' => 'customer@gmail.com',
            'status' => 'active',
            'document_number' => '23-29834800-4',
        ]);
        $user_app->save();

        expect('Last use is not defined', $model->lastMobileAppUse())->isEmpty();

        $user_app->addCustomer($model, true);
        UserAppActivity::createInstallationRegister($user_app->user_app_id, true);

        expect('Last use is not empty', $model->lastMobileAppUse(true))->notEmpty();
        expect('Last use is today', $model->lastMobileAppUse(true))->equals((new \DateTime('now'))->format('Y-m-d'));
    }

    public function testCanSendSMSMessage()
    {
        $model = new Customer([
            'tax_condition_id' => 1,
            'publicity_shape' => 'web',
            'document_number' => '23-29834800-4',
            'name' => 'Cliente1',
            'document_type_id' => 1,
            'customerClass' => 1,
            '_notifications_way' => [Customer::getNotificationWays()],
            'code' => 11111,
            'email' => 'customer@gmail.com',
            'status' => 'enabled'
        ]);
        $model->save();

        expect('Customer can send messages', $model->canSendSMSMessage())->true();

        $sms_per_customer = Config::getValue('sms_per_customer');

        for($i=0; $i<$sms_per_customer +1; $i++){
            $customer_message = new CustomerHasCustomerMessage([
                'customer_id' => $model->customer_id,
                'customer_message_id' => 1,
                'timestamp' => (new \DateTime('now'))->getTimestamp()
            ]);
            $customer_message->save();
        }

        expect('Customer cant send more messages', $model->canSendSMSMessage())->false();
    }

    public function testSendMobileAppLinkSMSMessage() {
        $model = new Customer([
            'tax_condition_id' => 1,
            'publicity_shape' => 'web',
            'document_number' => '23-29834800-4',
            'document_type_id' => 1,
            'customerClass' => 1,
            'name' => 'Cliente1',
            '_notifications_way' => [Customer::getNotificationWays()],
            'code' => 11111,
            'email' => 'customer@gmail.com',
            'phone' => '2612575620',
            'status' => 'enabled'
        ]);
        $model->save();

        Config::setValue('link-to-app-customer-message-id', 1);

        expect('Can send SMS message to customer', $model->sendMobileAppLinkSMSMessage())->true();
    }

    public function testDontSendMobileAppLinkSMSMessageWhenIsDisabled() {
        $model = new Customer([
            'tax_condition_id' => 1,
            'publicity_shape' => 'web',
            'document_number' => '23-29834800-4',
            'document_type_id' => 1,
            'customerClass' => 1,
            'name' => 'Cliente1',
            '_notifications_way' => [Customer::getNotificationWays()],
            'code' => 11111,
            'email' => 'customer@gmail.com',
            'phone' => '2612575620',
            'status' => 'enabled'
        ]);
        $model->save();

        Config::setValue('link-to-app-customer-message-id', 1);

        $customer_message = CustomerMessage::findOne(1);
        $customer_message->updateAttributes(['status' => Customer::STATUS_DISABLED]);

        expect('Cant send SMS message to customer', $model->sendMobileAppLinkSMSMessage())->false();
    }

    public function testGetStatusEmailForSelect()
    {
        $seleccion = Customer::getStatusEmailForSelect();

        expect('Get status email for select is an array', is_array($seleccion))->true();
        expect('Active is in array', array_key_exists( Customer::EMAIL_STATUS_ACTIVE, $seleccion))->true();
        expect('Invalid is in array', array_key_exists(Customer::EMAIL_STATUS_INVALID, $seleccion))->true();
        expect('Inactive is in array', array_key_exists(Customer::EMAIL_STATUS_INACTIVE, $seleccion))->true();
        expect('Bounced is in array', array_key_exists( Customer::EMAIL_STATUS_BOUNCED, $seleccion))->true();
    }

    public function testHasDraftBills()
    {
        $model = new Customer([
            'tax_condition_id' => 1,
            'publicity_shape' => 'web',
            'document_number' => '12456789',
            'document_number' => '23-29834800-4',
            'name' => 'Cliente1',
            'document_type_id' => 1,
            'customerClass' => 1,
            '_notifications_way' => [Customer::getNotificationWays()],
        ]);
        $model->save();

        $bill = Bill::findOne(1);
        $bill->updateAttributes(['customer_id' => $model->customer_id, 'status' => Bill::STATUS_CLOSED]);

        expect('Customer doesnt have any draft bill', $model->hasDraftBills())->false();

        $bill->updateAttributes(['status' => Bill::STATUS_DRAFT]);

        expect('Customer has one draft bill', $model->hasDraftBills())->true();
    }

    public function testHasDraftPayments()
    {
        $model = new Customer([
            'tax_condition_id' => 1,
            'publicity_shape' => 'web',
            'document_number' => '12456789',
            'document_number' => '23-29834800-4',
            'document_type_id' => 1,
            'name' => 'Cliente1',
            'customerClass' => 1,
            '_notifications_way' => [Customer::getNotificationWays()],
        ]);
        $model->save();

        $payment = Payment::findOne(1);
        $payment->updateAttributes(['customer_id' => $model->customer_id, 'status' => Payment::PAYMENT_CLOSED]);

        expect('Customer doesnt have any draft payment', $model->hasDraftPayments())->false();

        $payment->updateAttributes(['status' => Payment::PAYMENT_DRAFT]);

        expect('Customer has one draft payment', $model->hasDraftPayments())->true();
    }

    public function testGetActiveCustomerHasDiscounts()
    {
        $model = new Customer([
            'tax_condition_id' => 1,
            'publicity_shape' => 'web',
            'document_number' => '12456789',
            'document_number' => '23-29834800-4',
            'document_type_id' => 1,
            'name' => 'Cliente1',
            'customerClass' => 1,
            '_notifications_way' => [Customer::getNotificationWays()],
        ]);
        $model->save();

        $discount = new Discount([
            'name' => 'Descuento1',
            'status' => Discount::STATUS_ENABLED,
            'type' => Discount::TYPE_FIXED,
            'value' => 50,
            'value_from' => 'plan',
            'from_date' => (new \DateTime('now'))->modify('-1 month')->format('d-m-Y'),
            'to_date' => (new \DateTime('now'))->modify('+1 month')->format('d-m-Y'),
            'periods' => 1,
            'apply_to' => Discount::APPLY_TO_PRODUCT,
            'referenced' => 1,
        ]);
        $discount->save();

        $chd = new CustomerHasDiscount([
            'customer_id' => $model->customer_id,
            'discount_id' => $discount->discount_id,
            'from_date' => (new \DateTime('now'))->modify('-1 month')->format('d-m-Y'),
            'to_date' => (new \DateTime('now'))->modify('+1 month')->format('d-m-Y'),
            'status' => CustomerHasDiscount::STATUS_ENABLED
        ]);
        $chd->save();

        expect('Get one discount', count($model->getActiveCustomerHasDiscounts()->all()))->equals(1);

        $chd->updateAttributes(['status' => CustomerHasDiscount::STATUS_DISABLED]);

        expect('Get any discount', count($model->getActiveCustomerHasDiscounts()->all()))->equals(0);

        $chd->updateAttributes(['status' => CustomerHasDiscount::STATUS_ENABLED]);
        $discount->updateAttributes(['status' => Discount::STATUS_DISABLED]);

        expect('Get any discount', count($model->getActiveCustomerHasDiscounts()->all()))->equals(0);

        $discount->updateAttributes(['status' =>  Discount::STATUS_ENABLED]);
        $chd->updateAttributes(['from_date' => (new \DateTime('now'))->modify('+1 days')->format('Y-m-d')]);

        expect('Get any discount', count($model->getActiveCustomerHasDiscounts()->all()))->equals(0);
    }

    public function testIsNewCustomer()
    {
        $days_qty = Config::getValue('new_contracts_days');

        $model = new Customer([
            'tax_condition_id' => 1,
            'publicity_shape' => 'web',
            'document_number' => '12456799',
            'document_number' => '23-29834800-4',
            'document_type_id' => 1,
            'name' => 'Cliente1',
            'customerClass' => 1,
            '_notifications_way' => [Customer::getNotificationWays()],
        ]);
        $model->save();

        $old_date = $days_qty + 1;
        $new_date = $days_qty - 1;

        $contract = new Contract([
            'date' => (new \DateTime('now'))->format('d-m-Y'),
            'customer_id' => $model->customer_id,
            'from_date' => (new \DateTime('now'))->modify("-$old_date days")->format('d-m-Y')
        ]);
        $contract->save();

        $model->refresh();
        $contract->refresh();

        expect('Customer is not new', $model->isNewCustomer())->false();

        $contract->from_date = (new \DateTime('now'))->modify("-$new_date days")->format('d-m-Y');
        $contract->to_date = (new \DateTime('now'))->modify("+1 month")->format('d-m-Y');
        $contract->save();
        $model->refresh();
        $contract->refresh();

        expect('Customer is new', $model->isNewCustomer())->true();

        $contract->from_date = (new \DateTime('now'))->modify("-$days_qty days")->format('d-m-Y');
        $contract->to_date = (new \DateTime('now'))->modify("+1 month")->format('d-m-Y');
        $contract->save();
        $model->refresh();
        $contract->refresh();

        expect('Customer is not new 2', $model->isNewCustomer())->false();
    }

    public function testHasfibraPlan()
    {
        $model = new Customer([
            'tax_condition_id' => 1,
            'publicity_shape' => 'web',
            'document_number' => '12456799',
            'document_number' => '23-29834800-4',
            'document_type_id' => 1,
            'name' => 'Cliente1',
            'customerClass' => 1,
            '_notifications_way' => [Customer::getNotificationWays()],
        ]);
        $model->save();

        $contract = new Contract([
            'date' => (new \DateTime('now'))->format('d-m-Y'),
            'customer_id' => $model->customer_id,
            'from_date' => (new \DateTime('now'))->format('d-m-Y')
        ]);
        $contract->save();

        $model->refresh();
        $contract->refresh();

        $contract->addContractDetail([
            'contract_id' => $contract->contract_id,
            'product_id' => 1,
            'count' => 1,
            'vendor_id' => 1
        ]);

        expect('Has fibra plan return false', $model->hasFibraPlan())->false();

        $category_fibra = \app\modules\sale\models\Category::findOne(['system' => 'plan-fibra']);
        $product_has_category = new ProductHasCategory([
            'product_id' => 1,
            'category_id' => $category_fibra->category_id,
        ]);
        $product_has_category->save();

        expect("Has fibra plan return true", $model->hasFibraPlan())->true();
    }


    public function testValidateDniSuccess8Characters()
    {
        $document_type = DocumentType::findOne(['name' => 'DNI']);

        if (empty($document_type)) {
            expect('Document Type not found', false)->true();
        }

        $customer = new Customer();
        $customer->document_type_id = $document_type->document_type_id;
        $customer->document_number = "35875225";

        expect('Not validated', $customer->validate(['document_number']))->true();

    }


    public function testValidateDniSuccess7Characters()
    {
        $document_type = DocumentType::findOne(['name' => 'DNI']);

        if (empty($document_type)) {
            expect('Document Type not found', false)->true();
        }

        $customer = new Customer();
        $customer->document_type_id = $document_type->document_type_id;
        $customer->document_number = "5875225";

        expect('Not validated', $customer->validate(['document_number']))->true();

    }

    public function testValidateCuitSuccess()
    {
        $document_type = DocumentType::findOne(['name' => 'CUIT']);

        if (empty($document_type)) {
            expect('Document Type not found', false)->true();
        }

        $customer = new Customer();
        $customer->document_type_id = $document_type->document_type_id;
        $customer->document_number = "20358752250";

        expect('Not validated', $customer->validate(['document_number']))->true();

    }

    public function testValidateDniFailLessCharacters()
    {
        $document_type = DocumentType::findOne(['name' => 'DNI']);

        if (empty($document_type)) {
            expect('Document Type not found', false)->true();
        }

        $customer = new Customer();
        $customer->document_type_id = $document_type->document_type_id;
        $customer->document_number = "20358752250";

        expect('Not validated', $customer->validate(['document_number']))->false();

    }

    public function testValidateDniFailSameCharacter()
    {
        $document_type = DocumentType::findOne(['name' => 'DNI']);

        if (empty($document_type)) {
            expect('Document Type not found', false)->true();
        }

        $customer = new Customer();
        $customer->document_type_id = $document_type->document_type_id;
        $customer->document_number = "99999";

        expect('Not validated', $customer->validate(['document_number']))->false();

    }

    public function testValidatePhonesOnlyNumbers()
    {
        $customer = new Customer();
        $customer->phone = '2616260580';
        $customer->phone2 = '2616260581';
        $customer->phone3 = '2616260582';
        $customer->phone4 = '2616260583';

        $validate = $customer->validate(['phone', 'phone2', 'phone3', 'phone4']);

        \Codeception\Util\Debug::debug($customer->getErrors());

        expect('Not validated', $validate)->true();
    }

    public function testValidatePhonesFail()
    {
        $customer = new Customer();
        $customer->phone = '2616260580 :)';
        $customer->phone2 = '2616260581 (El de mi mujer)';
        $customer->phone3 = '2616260582';
        $customer->phone4 = '2616260583';

        expect('Not validated', $customer->validate(['phone', 'phone2', 'phone3', 'phone4']))->false();
    }

    public function testValidatePhone2RequiredSuccessOnInsert()
    {
        $customer = new Customer();
        $customer->scenario = 'insert';
        $customer->phone2 = '2616260580';

        expect('Not validate', $customer->validate(['phone2']))->true();
    }

    public function testValidatePhone2RequiredFailOnInsert()
    {
        $customer = new Customer();
        $customer->scenario = 'insert';

        expect('Validate', $customer->validate(['phone2']))->false();
    }

    public function testValidatePhone2NotRequiredWhenNullOnUpdate()
    {
        $customer = Customer::findOne(45904);

        expect('Not validate', $customer->validate(['phone2']))->true();
    }

    public function testValidatePhone2RequiredWhenNotNullOnUpdate()
    {
        $customer = Customer::findOne(45903);
        $customer->phone2 = null;

        expect('Not validate', $customer->validate(['phone2']))->false();
    }

    public function testValidatePhone3RequiredSuccessOnInsert()
    {
        $customer = new Customer();
        $customer->scenario = 'insert';
        $customer->phone3 = '2616260580';

        expect('Not validate', $customer->validate(['phone3']))->true();
    }

    public function testValidatePhone3RequiredFailOnInsert()
    {
        $customer = new Customer();
        $customer->scenario = 'insert';

        expect('Validate', $customer->validate(['phone3']))->false();
    }

    public function testValidatePhone3NotRequiredWhenNullOnUpdate()
    {
        $customer = Customer::findOne(45904);

        expect('Not validate', $customer->validate(['phone3']))->true();
    }

    public function testValidatePhone3RequiredWhenNotNullOnUpdate()
    {
        $customer = Customer::findOne(45903);
        $customer->phone3 = null;

        expect('Not validate', $customer->validate(['phone3']))->false();
    }

    public function testGeneratePaymentCode3Digits() {
        $customer = new Customer();
        $company = Company::findOne(7);

        $paymentCode = $customer->generatePaymentCode($company, 518);

        expect('Error al generar código de pago', strlen((string)$paymentCode))->equals(14);
    }

    public function testGeneratePaymentCode4Digits() {
        $customer = new Customer();
        $company = Company::findOne(7);

        $paymentCode = $customer->generatePaymentCode($company, 4518);

        expect('Error al generar código de pago', strlen((string)$paymentCode))->equals(14);
    }

    public function testGeneratePaymentCode5Digits() {
        $customer = new Customer();
        $company = Company::findOne(7);

        $paymentCode = $customer->generatePaymentCode($company, 14518);

        expect('Error al generar código de pago', strlen((string)$paymentCode))->equals(14);
    }

    public function testGeneratePaymentCode6Digits() {
        $customer = new Customer();
        $company = Company::findOne(7);

        $paymentCode = $customer->generatePaymentCode($company, 114518);

        expect('Error al generar código de pago', strlen((string)$paymentCode))->equals(14);
    }

    public function testGetLastClosedBill()
    {
        $customer = Customer::findOne(45904);
        $bill1 = Bill::findOne(10);
        $bill1->updateAttributes(['status' => Bill::STATUS_DRAFT]);
        $bill2 = Bill::findOne(11);
        $bill2->updateAttributes(['status' => Bill::STATUS_DRAFT]);

        expect('Get last closed bill returns false', $customer->getLastClosedBill())->false();

        $bill1->updateAttributes(['status' => Bill::STATUS_CLOSED]);

        $lastClosedBill = $customer->getLastClosedBill();
        expect('Get last closed bill returns instance of bill_1', $lastClosedBill)->isInstanceOf(Bill::class);
        expect('Get last closed bill returns bill_1', $lastClosedBill->bill_id)->equals(10);

        $bill1->updateAttributes(['status' => Bill::STATUS_DRAFT]);
        $bill2->updateAttributes(['status' => Bill::STATUS_CLOSED]);

        $lastClosedBill = $customer->getLastClosedBill();
        expect('Get last closed bill returns instance of bill_2', $lastClosedBill)->isInstanceOf(Bill::class);
        expect('Get last closed bill returns bill_2', $lastClosedBill->bill_id)->equals(11);

        $bill1->updateAttributes(['status' => Bill::STATUS_CLOSED]);
        $bill2->updateAttributes(['status' => Bill::STATUS_CLOSED]);

        $lastClosedBill = $customer->getLastClosedBill();
        expect('Get last closed bill returns instance of bill_2', $lastClosedBill)->isInstanceOf(Bill::class);
        expect('Get last closed bill returns bill_2', $lastClosedBill->bill_id)->equals(11);
    }


    //TODO resto de la clase
}