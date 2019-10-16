<?php

use app\modules\checkout\models\CompanyHasPaymentTrack;
use app\modules\cobrodigital\models\PaymentCard;
use app\modules\sale\models\Customer;
use app\modules\sale\models\CustomerHasCustomerMessage;
use app\tests\fixtures\CustomerCategoryFixture;
use app\tests\fixtures\TaxConditionFixture;
use app\tests\fixtures\CustomerClassFixture;
use app\tests\fixtures\DocumentTypeFixture;
use app\modules\sale\models\DocumentType;
use app\modules\config\models\Config;
use app\tests\fixtures\CustomerFixture;
use app\tests\fixtures\BillFixture;
use app\tests\fixtures\PaymentFixture;
use app\modules\ticket\models\Category;
use app\modules\ticket\models\Ticket;
use app\tests\fixtures\TicketStatusFixture;
use app\tests\fixtures\EmptyAdsFixture;
use app\modules\westnet\models\EmptyAds;
use app\modules\sale\models\Company;
use app\tests\fixtures\CompanyHasPaymentTrackFixture;
use app\modules\checkout\models\PaymentMethod;
use app\modules\sale\models\CustomerHasPaymentTrack;
use app\modules\checkout\models\Track;
use app\modules\mobileapp\v1\models\UserApp;
use app\modules\mobileapp\v1\models\UserAppActivity;
use app\tests\fixtures\CustomerMessageFixture;
use app\modules\sale\models\Bill;
use app\modules\checkout\models\Payment;
use app\modules\sale\models\CustomerHasDiscount;
use app\modules\sale\models\Discount;

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
            'empty_ads' => [
                'class' => EmptyAdsFixture::class
            ],
            'company_has_payment_track' => [
                'class' => CompanyHasPaymentTrackFixture::class
            ],
            'document_type_id' => [
                'class' => DocumentTypeFixture::class
            ],
            'customer_message' => [
                'class' => CustomerMessageFixture::class
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

        $model->validate();
        \Codeception\Util\Debug::debug($model->getErrors());

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
        $response = Customer::hasCategoryTicket(59809, $category->category_id, true);
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
    }

    public function testAssociateEmptyAds()
    {
        EmptyAds::updateAll(['used' => 1]);

        $model = new Customer([
            'tax_condition_id' => 1,
            'publicity_shape' => 'web',
            'document_number' => '27381010673',
            'document_type_id' => 1,
            'customerClass' => 1,
            'company_id' => 1,
            '_notifications_way' => [Customer::getNotificationWays()],
        ]);
        $model->save();

        expect('Cant associate empty ads if ads_code doesnt exists', $model->associateEmptyADS(55))->false();

        $empty_ads_without_payment_card = EmptyAds::find()->where(['payment_card_id' => null])->one();
        $empty_ads_without_payment_card->updateAttributes(['used' => 0]);

        $associate_empty_ads = $model->associateEmptyADS($empty_ads_without_payment_card->code);
        expect('Associate empty ads successfully', $associate_empty_ads)->true();
        expect('Customer code has been chanched', $model->code)->equals($empty_ads_without_payment_card->code);
        expect('Customer company has been changed', $model->company_id)->equals($empty_ads_without_payment_card->company_id);
        expect('Customer payment code 19 digits is empty', $model->payment_code_19_digits)->isEmpty();
        expect('Customer payment code 29 digits is empty', $model->payment_code_29_digits)->isEmpty();

        EmptyAds::updateAll(['used' => 1]);

        $model2 = new Customer([
            'tax_condition_id' => 1,
            'publicity_shape' => 'web',
            'document_number' => '27381010673',
            'document_type_id' => 1,
            'customerClass' => 1,
            '_notifications_way' => [Customer::getNotificationWays()],
        ]);
        $model2->save();

        $empty_ads_with_payment_card = EmptyAds::find()->where(['not',['payment_card_id' => null]])->one();
        $empty_ads_with_payment_card->updateAttributes(['used' => 0]);

        $associate_empty_ads = $model->associateEmptyADS($empty_ads_with_payment_card->code);
        expect('Associate empty ads successfully', $associate_empty_ads)->true();
        expect('Customer code has been chanched', $model->code)->equals($empty_ads_with_payment_card->code);
        expect('Customer company has been changed', $model->company_id)->equals($empty_ads_with_payment_card->company_id);
        expect('Customer payment code 19 digits', $model->payment_code_19_digits)->equals($empty_ads_with_payment_card->paymentCard->code_19_digits);
        expect('Customer payment code 29 digits', $model->payment_code_29_digits)->equals($empty_ads_with_payment_card->paymentCard->code_29_digits);

    }

    public function testGetAvailablePaymentTracks()
    {
        $model = new Customer([
            'tax_condition_id' => 1,
            'publicity_shape' => 'web',
            'document_number' => '27381010673',
            'document_type_id' => 1,
            'customerClass' => 1,
            'company_id' => 1,
            '_notifications_way' => [Customer::getNotificationWays()],
        ]);
        $model->save();

        $company = Company::findOne(1);
        $payment_methods = PaymentMethod::find()->all();

        foreach ($payment_methods as $payment_method) {
            $company_payment_track = $company->getPaymentEnabledTracks()->where(['payment_method_id' => $payment_method->payment_method_id])->one();
            $customer_payment_track = $model->getAvailablePaymentTracks()->where(['payment_method_id' => $payment_method->payment_method_id])->one();

            if($company_payment_track && $customer_payment_track) {
                expect("Payment method $payment_method->payment_method_id has same track id", $company_payment_track
                    ->track_id)->equals($customer_payment_track
                    ->track_id);
            }
        }

        foreach ($payment_methods as $payment_method) {
            $customer_has_payment_track = new CustomerHasPaymentTrack([
                'customer_id' => $model->customer_id,
                'payment_method_id' => $payment_method->payment_method_id,
                'track_id' => 2
            ]);
            $customer_has_payment_track->save();
        }

        foreach ($payment_methods as $payment_method) {
            $customer_payment_track = $model->getAvailablePaymentTracks()->where(['payment_method_id' => $payment_method->payment_method_id])->one();

            expect("Payment method $payment_method->payment_method_id has not same track id", $customer_payment_track->track_id)->equals(2);
        }
    }

    public function testAssociatePaymentCard() {
        $model = new Customer([
            'tax_condition_id' => 1,
            'publicity_shape' => 'web',
            'document_number' => '27381010673',
            'document_type_id' => 1,
            'customerClass' => 1,
            'company_id' => 2,
            '_notifications_way' => [Customer::getNotificationWays()],
        ]);
        $model->save();

        foreach (Track::find()->all() as $track) {
            $track->updateAttributes(['use_payment_card' => 0]);
        }

        expect('Cant associate payment card cause company doesnt have a track with payment cards', $model->associatePaymentCard())->false();

        foreach (Track::find()->all() as $track) {
            $track->updateAttributes(['use_payment_card' => 1]);
        }

        $unused_payment_cards = count(PaymentCard::find()->where(['used' => 0])->all());

        expect('Associate card successfully', $model->associatePaymentCard())->true();
        expect('Customer has 19 digits payment code', $model->payment_code_19_digits)->notEmpty();
        expect('Customer has 29 digits payment code', $model->payment_code_29_digits)->notEmpty();
        expect('Payment card mark as used', count(PaymentCard::find()->where(['used' => 0])->all()))->equals($unused_payment_cards - 1);

        $code_19 = $model->payment_code_19_digits;
        $code_29 = $model->payment_code_29_digits;

        //Ya no deberia volver a asociar una tarjeta
        expect('Cant associate payment card again', $model->associatePaymentCard())->false();
        expect('Payment code 19 digits didnt change', $model->payment_code_19_digits)->equals($code_19);
        expect('Payment code 29 digits didnt change', $model->payment_code_29_digits)->equals($code_29);
        expect('Payment card quantity', count(PaymentCard::find()->where(['used' => 0])->all()))->equals($unused_payment_cards - 1);

    }

    public function testGetPaymentMethodNameAndCodes()
    {
        $model = new Customer([
            'tax_condition_id' => 1,
            'publicity_shape' => 'web',
            'document_number' => '27381010673',
            'document_type_id' => 1,
            'customerClass' => 1,
            'company_id' => 1,
            'email' => 'customer@gmail.com',
            'status' => 'enabled',
            '_notifications_way' => [Customer::getNotificationWays()],
        ]);
        $model->save();

        $payment_method_name_exists = false;

        foreach ($model->availablePaymentTracks as $payment_track) {
            foreach ($model->getPaymentMethodNameAndCodes() as $payment_name_and_code) {

                if($payment_track->paymentMethod->name == $payment_name_and_code['payment_method_name']) {
                    $payment_method_name_exists = true;
                }
            }
        }

        expect('Payment metod exists in array', $payment_method_name_exists)->true();

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
        $user_app->activity->updateAttributes(['last_activity_datetime' => $old_last_activity]);

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
            'persistent' => null
        ]);
        $discount->save();

        $chd = new CustomerHasDiscount([
            'customer_id' => $model->customer_id,
            'discount_id' => $discount->discount_id,
            'from_date' => (new \DateTime('now'))->modify('-1 month +3 days')->format('d-m-Y'),
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

    //TODO resto de la clase
}