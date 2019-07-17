<?php
/**
 * Created by PhpStorm.
 * User: dexterlab10
 * Date: 03/07/19
 * Time: 16:00
 */

use app\modules\sale\models\InvoiceClass;

class InvoiceClassTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _fixtures()
    {
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new InvoiceClass();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new InvoiceClass([
            'name' => 'AAA',
            'class' => 'app\modules\invoice\components\einvoice\afip\fev1\Fev1'
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new InvoiceClass();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new InvoiceClass([
            'name' => 'AAA',
            'class' => 'app\modules\invoice\components\einvoice\afip\fev1\Fev1'
        ]);

        expect('Saved when full and new', $model->save())->true();
    }

}