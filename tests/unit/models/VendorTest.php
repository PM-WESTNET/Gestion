<?php

use app\modules\westnet\models\Vendor;
use app\tests\fixtures\DocumentTypeFixture;
use app\tests\fixtures\AddressFixture;
use app\tests\fixtures\AccountFixture;
use app\tests\fixtures\VendorCommissionFixture;
use app\tests\fixtures\ProviderFixture;

class VendorTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _after()
    {
    }

    public function _fixtures()
    {
        return [
            'document_type' => [
                'class' => DocumentTypeFixture::class
            ],
            'address' => [
                'class' => AddressFixture::class
            ],
            'account' => [
                'class' => AccountFixture::class
            ],
            'vendor_commission' => [
                'class' => VendorCommissionFixture::class
            ],
            'provider' => [
                'class' => ProviderFixture::class
            ],
        ];
    }

    public function testInvalidWhenNewAndEmpty()
    {
        $model = new Vendor();

        $this->assertFalse($model->validate());
    }

    public function testValidWhenNewAndFull()
    {
        $model = new Vendor();
        $model->document_type_id = 1;
        $model->address_id = 1;
        $model->account_id = 1;
        $model->vendor_commission_id = 1;
        $model->external_user_id = 1;
        $model->provider_id = 149;

        $this->assertTrue($model->validate());
    }

    public function testNotSaveWhenNewAndEmpty()
    {
        $model = new Vendor();

        $this->assertFalse($model->save());
    }

    public function testSaveWhenNewAndFull()
    {
        $model = new Vendor();
        $model->document_type_id = 1;
        $model->address_id = 1;
        $model->account_id = 1;
        $model->vendor_commission_id = 1;
        $model->external_user_id = 1;
        $model->provider_id = 149;

        $this->assertTrue($model->save());
    }

    public function testGetFullName()
    {
        $model = new Vendor();
        $model->name = 'Juan';
        $model->lastname = 'Perez';
        $model->document_type_id = 1;
        $model->address_id = 1;
        $model->account_id = 1;
        $model->vendor_commission_id = 1;
        $model->external_user_id = 1;
        $model->provider_id = 149;

        $this->assertTrue(strpos($model->getFullName(), "erez, Juan") != false);
    }

    public function testGetForSelect()
    {
        $model = new Vendor([
            'name' => 'Raul',
            'lastname' => 'Cerra',
            'document_type_id' => 1,
            'address_id' => 1,
            'account_id' => 1,
            'vendor_commission_id' => 1,
            'external_user_id' => 1,
            'provider_id' => 149
        ]);

        $model_2 = new Vendor([
            'name' => 'Josefina',
            'lastname' => 'Aguero',
            'document_type_id' => 1,
            'address_id' => 1,
            'account_id' => 1,
            'vendor_commission_id' => 1,
            'external_user_id' => 1,
            'provider_id' => 149
        ]);
        $model->save();
        $model_2->save();

        $vendors = Vendor::findForSelect();

        expect('Get for select vendor 1', $vendors[$model->vendor_id])->equals('Cerra, Raul');
        expect('Get for select vendor 1', $vendors[$model_2->vendor_id])->equals('Aguero, Josefina');

    }

    public function testGetExternalVendors()
    {
        $model = new Vendor([
            'name' => 'Raul',
            'lastname' => 'Cerra',
            'document_type_id' => 1,
            'address_id' => 1,
            'account_id' => 1,
            'vendor_commission_id' => 1,
            'external_user_id' => 1,
            'provider_id' => 149
        ]);

        $model_2 = new Vendor([
            'name' => 'Josefina',
            'lastname' => 'Aguero',
            'document_type_id' => 1,
            'address_id' => 1,
            'account_id' => 1,
            'vendor_commission_id' => 1,
            'external_user_id' => null,
            'provider_id' => 149
        ]);
        $model->save();
        $model_2->save();

        $vendors = Vendor::getExternalVendors();
        expect('Get external vendors', count($vendors))->equals(1);
    }
}