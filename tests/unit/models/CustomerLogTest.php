<?php
/**
 * Created by PhpStorm.
 * User: dexterlab10
 * Date: 03/07/19
 * Time: 16:00
 */

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
use app\modules\config\models\Category;
use app\modules\ticket\models\Ticket;
use app\tests\fixtures\TicketStatusFixture;
use app\modules\mobileapp\v1\models\UserApp;
use app\modules\mobileapp\v1\models\UserAppActivity;
use app\tests\fixtures\CustomerMessageFixture;
use app\modules\sale\models\CustomerLog;
use app\tests\fixtures\UserFixture;

class CustomerLogTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _fixtures()
    {
        return [
            'customer' => [
                'class' => CustomerFixture::class
            ],
            'user' => [
                'class' => UserFixture::class
            ],
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new CustomerLog();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new CustomerLog([
            'action' => 'Alta de Datos de Cliente',
            'date' => (new \DateTime('now'))->format('Y-m-d'),
            'customer_id' => 45900,
            'user_id' => 1,
            'observations' => 'observation'
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new CustomerLog();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new CustomerLog([
            'action' => 'Alta de Datos de Cliente',
            'date' => (new \DateTime('now'))->format('Y-m-d'),
            'customer_id' => 45900,
            'user_id' => 1,
            'observations' => 'observation'
        ]);

        expect('Saved when full and new', $model->save())->true();
    }

}