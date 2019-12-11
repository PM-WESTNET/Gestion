<?php

use app\modules\checkout\models\PagoFacilTransmitionFile;
use app\tests\fixtures\CompanyFixture;
use app\tests\fixtures\CustomerFixture;
use app\tests\fixtures\MoneyBoxAccountFixture;
use app\tests\fixtures\MoneyBoxFixture;
use app\tests\fixtures\PartnerDistributionModelFixture;
use app\tests\fixtures\PaymentMethodFixture;
use yii\web\UploadedFile;

class PagoFacilTransmitionFileTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _fixtures(){
        return [
            'money_box_account' => [
                'class' => MoneyBoxAccountFixture::class
            ],
            'money_box' => [
                'class' => MoneyBoxFixture::class
            ],
            'customer' => [
                'class' => CustomerFixture::class
            ],
            'company' => [
                'class' => CompanyFixture::class
            ],
            'partner_distribution_model' => [
                'class' => PartnerDistributionModelFixture::class
            ],
            'payment_method' => [
                'class' => PaymentMethodFixture::class
            ]
        ];
    }

    public function loadFile()
    {
        $_FILES = [
            'PagoFacilTransmitionFile[file]' => [
                'name' => 'archivo_pago_facil.900',
                'type' => 'application/octet-stream',
                'size' => 826,
                'tmp_name' => __DIR__ . '/../../_resources/archivo_pago_facil.900',
                'error' => 0,
                'extension' => '900'
            ],
        ];
    }

    public function testInvalidWhenNewAndEmpty()
    {
        $model = new PagoFacilTransmitionFile();

        expect('Invalid when new and empty', $model->validate())->false();
    }

    public function testValidWhenNewAndFull()
    {
        $this->loadFile();

        $model = new PagoFacilTransmitionFile([
            'money_box_account_id' => 1,
            'money_box_id' => 1,
            'file' => UploadedFile::getInstanceByName("PagoFacilTransmitionFile[file]")
        ]);

        expect('Valid when new and full', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new PagoFacilTransmitionFile();

        expect('Not save when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $this->loadFile();

        $model = new PagoFacilTransmitionFile([
            'money_box_account_id' => 1,
            'money_box_id' => 1,
            'file' => UploadedFile::getInstanceByName('PagoFacilTransmitionFile[file]'),
            'file_name' => '/../../_resources/archivo_pago_facil.900',
            'upload_date' => (new \DateTime('now'))->format('d-m-Y')
        ]);

        expect('Save when full and new', $model->save())->true();
    }

    public function testIsRepeat()
    {
        $this->loadFile();

        $model = new PagoFacilTransmitionFile([
            'money_box_account_id' => 1,
            'money_box_id' => 1,
            'file' => UploadedFile::getInstanceByName('PagoFacilTransmitionFile[file]'),
            'file_name' => 'file1',
            'upload_date' => (new \DateTime('now'))->format('d-m-Y')
        ]);

        expect('Its not repeat', $model->isRepeat())->false();
        $model->save();

        $model2 = new PagoFacilTransmitionFile([
            'money_box_account_id' => 1,
            'money_box_id' => 1,
            'file' => UploadedFile::getInstanceByName('PagoFacilTransmitionFile[file]'),
            'file_name' => 'file1',
            'upload_date' => (new \DateTime('now'))->format('d-m-Y')
        ]);

        expect('Its repeat', $model->isRepeat())->true();
    }

    public function testImport()
    {
        $this->loadFile();

        Yii::setAlias('@webroot', __DIR__ . '/../../_resources/');

        $model = new PagoFacilTransmitionFile([
            'money_box_account_id' => 1,
            'money_box_id' => 1,
            'file' => UploadedFile::getInstanceByName('PagoFacilTransmitionFile[file]'),
            'file_name' => 'archivo_pago_facil.900',
            'upload_date' => (new \DateTime('now'))->format('d-m-Y')
        ]);
        $model->save();

        $result = $model->import();
        expect('Import file fails. Customer doenst have company_id', $result['status'])->false();

        $customer = \app\modules\sale\models\Customer::findOne(['code' => 59809]);
        $customer->updateAttributes(['company_id' => 2]);
        $customer->refresh();

        $result = $model->import();
        expect('Import file', $result['status'])->true();
    }

    public function testConfirmFile()
    {
        $this->loadFile();

        $model = new PagoFacilTransmitionFile([
            'money_box_account_id' => 1,
            'money_box_id' => 1,
            'file' => UploadedFile::getInstanceByName('PagoFacilTransmitionFile[file]'),
            'file_name' => 'archivo_pago_facil.900',
            'upload_date' => (new \DateTime('now'))->format('d-m-Y')
        ]);
        $model->save();

        $model->confirmFile();
        $model->refresh();

        expect('Status is pending', $model->status)->equals(PagoFacilTransmitionFile::STATUS_PENDING);
    }

    public function testGetPendingClosePaymentProcess()
    {
        $this->loadFile();

        $model = new PagoFacilTransmitionFile([
            'money_box_account_id' => 1,
            'money_box_id' => 1,
            'file' => UploadedFile::getInstanceByName('PagoFacilTransmitionFile[file]'),
            'file_name' => 'archivo_pago_facil.900',
            'upload_date' => (new \DateTime('now'))->format('d-m-Y')
        ]);
        $model->save();

        expect('No pending files when pago facil transmition file its not found', PagoFacilTransmitionFile::getPendingClosePaymentProcess(10000))->false();

        expect('No pending files', PagoFacilTransmitionFile::getPendingClosePaymentProcess($model->pago_facil_transmition_file_id))->false();

        $model->confirmFile();

        expect('Pending files', PagoFacilTransmitionFile::getPendingClosePaymentProcess($model->pago_facil_transmition_file_id))->true();
    }
}