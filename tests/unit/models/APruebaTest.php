<?php

use app\tests\fixtures\TaxConditionFixture;

//Test para probar si la configuracion toma los fixtures

class APruebaTest extends \Codeception\Test\Unit
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

    public function _fixtures(){
        return [
            'tax_condition' => [
                'class' => TaxConditionFixture::class,
            ]
        ];
    }
    // tests
    public function testSomeFeature()
    {
        $model = $this->tester->grabFixture('tax_condition', 1);
        \Codeception\Util\Debug::debug($model->name);
        $this->assertNotEmpty($model);
    }
}