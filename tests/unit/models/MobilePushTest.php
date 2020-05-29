<?php

use app\modules\mobileapp\v1\models\MobilePush;
use app\modules\mobileapp\v1\models\MobilePushHasUserApp;
use app\tests\fixtures\UserAppHasCustomerFixture;
use app\tests\fixtures\UserAppFixture;

class MobilePushTest extends \Codeception\Test\Unit
{
    protected function _before()
    {

    }

    protected function _after()
    {

    }

    public function _fixtures()
    {
        return [
            'user_app' => [
                'class' => UserAppFixture::class
            ],
            'user_app_has_customer' => [
                'class' => UserAppHasCustomerFixture::class
            ]
        ];
    }

    public function testInvalidWhenNewAndEmpty()
    {
        $model = new MobilePush();

        expect('Invalid when new and empty', $model->validate())->false();
    }

    public function testValidWhenNewAndFull()
    {
        $model = new MobilePush([
            'title' => 'Notificacion',
            'content' => 'Contenido',
        ]);

        expect('Valid when new and full', $model->validate())->true();
    }

    public function testNotSaveWhenNewAndEmpty()
    {
        $model = new MobilePush();

        expect('Not save when new and empty', $model->save())->false();
    }

    public function testSaveWhenNewAndFull()
    {
        $model = new MobilePush([
            'title' => 'Notificacion',
            'content' => 'Contenido',
        ]);

        expect('Save when new and full', $model->save())->true();
    }

    public function testAddUserApp()
    {
        $preview_mphua = MobilePushHasUserApp::find()->all();

        $model = new MobilePush([
            'title' => 'Notificacion',
            'content' => 'Contenido',
        ]);

        expect('Save when new and full', $model->save())->true();

        $model->addUserApp(45900, []);

        $mphua = count(MobilePushHasUserApp::find()->all());

        expect('MobilePushHasUserApp created', $mphua)->equals(count($preview_mphua) + 1);

        expect('MobilePushHasUserApp created and associated', count($model->mobilePushHasUserApps))->equals(1);

        $has_title = false;
        $has_content = false;

        foreach ($model->mobilePushHasUserApps as $userApp) {
            \Codeception\Util\Debug::debug($userApp);
            if($userApp->notification_title == 'Notificacion' && $userApp->notification_content == 'Contenido') {
                $has_title = true;
                $has_content = true;
            }
        }

        expect('MobilePushHasUserApp has title', $has_title)->true();
        expect('MobilePushHasUserApp has content', $has_content)->true();
    }

    public function testReplaceText()
    {
        $text = 'Texto con Alias @Nombre @PaymentCode @FacturasAdeudadas @ValorDeExtensionDePago @Saldo @CodigoDeCliente @TelefonoFijo @Celular1 @Celular2 @Celular3 y algo mas @EmailPrincipal @EmailSecundario';
        $data = [
            'name' => 'Nombre',
            'phone' => 'teléfono fijo',
            'phone2' => 'celular 1',
            'phone3' => 'celular 2',
            'phone4' => 'celular 3',
            'code' => 'codigo de cliente',
            'payment_code' => 'codigo de pago de cliente',
            'node' => 'nodo',
            'saldo' => '$saldo',
            'company_code' => 'codigo de empresa',
            'debt_bills' => 'facturas adeudadas',
            'product_extension_value' => 'valor de extension de pago',
            'email' => 'email primero',
            'email2' => 'email secundario',
        ];
        $string_expected = 'Texto con Alias Nombre codigo de pago de cliente facturas adeudadas valor de extension de pago $saldo codigo de cliente teléfono fijo celular 1 celular 2 celular 3 y algo mas email primero email secundario';

        expect('Returned text was replaced', MobilePush::replaceText($text, $data))->equals($string_expected);
    }

    public function testGetDataOneSignalFormatWithUserApps()
    {
        $model = new MobilePush([
            'title' => 'Notificacion',
            'content' => 'Contenido',
        ]);
        $model->save();
        $model->addUserApp(45900, []);

        $result = $model->getDataOneSignalFormat();

        expect('app_id key exists', array_key_exists('app_id', $result[0]))->true();
        expect('headings key exists', array_key_exists('headings', $result[0]))->true();
        expect('contents key exists', array_key_exists('contents', $result[0]))->true();
        expect('data key exists', array_key_exists('data', $result[0]))->true();
        expect('data key exists', array_key_exists('data', $result[0]))->true();
        expect('include_player_ids key exists', array_key_exists('include_player_ids', $result[0]))->true();
        expect('include_player_ids is correct', $result[0]['include_player_ids'])->equals('f0b2595a-90ef-4a99-af71-e1fbda905a53');
    }

    public function testGetDataOneSignalFormatWithoutUserApps()
    {
        $model = new MobilePush([
            'title' => 'Notificacion',
            'content' => 'Contenido',
        ]);
        $model->save();
        $result = $model->getDataOneSignalFormat();

        expect('app_id key exists', array_key_exists('app_id', $result[0]))->true();
        expect('headings key exists', array_key_exists('headings', $result[0]))->true();
        expect('contents key exists', array_key_exists('contents', $result[0]))->true();
        expect('data key exists', array_key_exists('data', $result[0]))->true();
        expect('data key exists', array_key_exists('data', $result[0]))->true();
        expect('included_segments key exists', array_key_exists('included_segments', $result[0]))->true();
        expect('included_segments is correct', $result[0]['included_segments'][0])->equals('All');
    }

    public function testSendThroughOneSignalWith()
    {
        $model = new MobilePush([
            'title' => 'Notificacion',
            'content' => 'Contenido',
        ]);
        $model->save();
        $model->addUserApp(45900, []);

        $data_formated = $model->getDataOneSignalFormat();
        $result = $model->sendThroughOneSignal($data_formated);

        expect('status key exists', array_key_exists('status', $result))->true();
        expect('errors key exists', array_key_exists('errors', $result))->true();
        expect('total_to_send key exists', array_key_exists('total_to_send', $result))->true();
        expect('total_sended key exists', array_key_exists('total_sended', $result))->true();
        expect('total_sended_with_errors key exists', array_key_exists('total_sended_with_errors', $result))->true();
        expect('total_not_sended key exists', array_key_exists('total_not_sended', $result))->true();
        expect('total_to_send is 1', $result['total_to_send'])->equals(1);
    }

    public function testSend()
    {
        $model = new MobilePush([
            'title' => 'Notificacion',
            'content' => 'Contenido',
        ]);
        $model->save();
        $model->addUserApp(45900, []);

        expect('Send returns true', $model->send())->true();
        expect('Mobile push status is sended', $model->status)->equals(MobilePush::STATUS_SENDED);
    }
}