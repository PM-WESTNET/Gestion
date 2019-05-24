<?php

use app\modules\checkout\models\CompanyHasPaymentTrack;
use app\modules\cobrodigital\models\PaymentCard;
use app\modules\sale\models\Customer;
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
            'tax_condition_id' => 3,
            'document_type_id' => 2,
            'publicity_shape' => 'web',
            'document_number' => '12456789',
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
            'document_number' => '27381010673',
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
            'document_number' => '12456789',
            'customerClass' => 1,
            '_notifications_way' => [Customer::getNotificationWays()],
        ]);

        expect('Get full name', $model->getFullName())->equals('Apellido, Nombre');
    }

    public function testHasDocumentType(){
        $model = new Customer([
            'tax_condition_id' => 1,
            'publicity_shape' => 'web',
            'document_number' => '12456789',
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
            'document_number' => '27381010673',
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
            'document_number' => '20175137611',
            'document_type_id' => 1,
            'customerClass' => 1,
            'customerCategory' => 1,
            '_notifications_way' => [Customer::getNotificationWays()],
        ]);
        $model2->scenario = 'insert';
        $model2->save();

        \Codeception\Util\Debug::debug(print_r($model2->getErrors(), 1));

        $updatedModel = Customer::findOne($model2->customer_id);

        $updatedModel->document_number = '20358752250';
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

    public function testForceCustomerCode()
    {
        $model = new Customer([
            'tax_condition_id' => 1,
            'publicity_shape' => 'web',
            'document_number' => '20175137611',
            'customerClass' => 1,
            'document_type_id' => 1,
            '_notifications_way' => [Customer::getNotificationWays()],
            'code' => 11111
        ]);
        $model->save();

        expect('Code cant be forced', $model->code)->notEquals(11111);
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

    public function testAssociateEmptyAds()
    {
        EmptyAds::updateAll(['used' => 1]);

        $model = new Customer([
            'tax_condition_id' => 1,
            'publicity_shape' => 'web',
            'document_number' => '27381010673',
            'document_type_id' => 1,
            'customerClass' => 1,
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

        expect('Cant associate payment card cause company doesnt have a track with payment cards', $model->associatePaymentCard())->false();

        $company_has_payment_track = CompanyHasPaymentTrack::find()->where(['company_id' => 2, 'payment_method_id' => 1, 'track_id' => 1, 'status' => 'disabled'])->one();
        $company_has_payment_track->updateAttributes(['track_id' => 2]);
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
    }
}