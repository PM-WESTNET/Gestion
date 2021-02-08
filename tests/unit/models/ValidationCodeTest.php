<?php namespace models;

use app\modules\config\models\Config;
use app\modules\mobileapp\v1\models\UserApp;
use app\modules\mobileapp\v1\models\ValidationCode;
use app\modules\westnet\notifications\models\InfobipMessage;
use app\tests\fixtures\UserAppHasCustomerFixture;

class ValidationCodeTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function _fixtures() {
        return  [
          [
              'class' => UserAppHasCustomerFixture::class
          ]
        ];
    }

    // tests
    public function testSMSText()
    {
        $code = new ValidationCode([
            'user_app_has_customer_id' => 1,
        ]);

        $user_app = UserApp::findOne(1);

        $code->save();

        $text = str_replace('{code}', $code->code, Config::getValue('sms_validation_content'));

        $code->sendCodeSms($user_app->destinatary);

        $infobipMessage = InfobipMessage::find()->orderBy(['infobip_message_id' => SORT_DESC])->one();

        expect('Failed', $infobipMessage->message)->equals($text);
    }
}