<?php

use app\modules\pagomiscuentas\models\PagomiscuentasFile;
use app\tests\fixtures\PartnerDistributionModelFixture;
use app\tests\fixtures\CustomerFixture;
use app\tests\fixtures\PaymentMethodFixture;
use app\tests\fixtures\CompanyFixture;
use app\tests\fixtures\BillTypeFixture;
use app\tests\fixtures\CurrencyFixture;
use app\modules\pagomiscuentas\models\PagomiscuentasFileHasBill;
use app\tests\fixtures\PointOfSaleFixture;
use app\tests\fixtures\CompanyHasBillTypeFixture;
USE app\modules\sale\components\BillExpert;
use yii\db\Expression;

class PagomiscuentasFileTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _before()
    {
        Yii::$app->db->createCommand('INSERT INTO `company_has_bill_type` (`company_id`, `bill_type_id`, `default`) VALUES (1, 1, 1)')->execute();
    }

    public function _after()
    {
        Yii::$app->db->createCommand('DELETE FROM`company_has_bill_type` WHERE `company_id` = 1 AND `bill_type_id` = 1 AND `default` = 1')->execute();
    }

    public function _fixtures(){
        return [
            'partner_distribution_model' => [
                'class' => PartnerDistributionModelFixture::class,
            ],
            'customer' => [
                'class' => CustomerFixture::class,
            ],
            'payment_method' => [
                'class' => PaymentMethodFixture::class
            ],
            'company' => [
                'class' =>  CompanyFixture::class,
            ],
            'bill_type' => [
                'class' => BillTypeFixture::class,
            ],
            'currency' => [
                'class' => CurrencyFixture::class,
            ],
            'point_of_sale' => [
                'class' => PointOfSaleFixture::class,
            ],
            'company_has_bill_type' => [
                'class' => CompanyHasBillTypeFixture::class
            ]
        ];
    }

    public function testInvalidWhenNew()
    {
        $model = new PagomiscuentasFile();

        expect('Save', $model->save())->false();
    }

    public function testSuccessSaveTypeBill()
    {
        $model= new PagomiscuentasFile([
            'company_id' => 1,
            'from_date' => '01-01-2019',
            'date' => '31-01-2019',
            'type' => PagomiscuentasFile::TYPE_BILL
        ]);

        expect('Not Saved', $model->save())->true();
    }

    public function testSuccessSaveTypePayment()
    {
        $model= new PagomiscuentasFile([
            'company_id' => 1,
            'date' => '31-01-2019',
            'type' => PagomiscuentasFile::TYPE_PAYMENT
        ]);

        expect('Not Saved', $model->save())->true();
    }

    public function testCreatePayment()
    {
        $model = new PagomiscuentasFile([
            'company_id' => 1,
            'from_date' => '01-01-2019',
            'date' => '31-01-2019',
            'type' => PagomiscuentasFile::TYPE_BILL
        ]);
        $model->save();

        expect('Create payment fails when type is bill', $model->createPayment(45900, (new \DateTime('now'))->format('Y-m-d'), 123, 'description', 1))->false();

        $model = new PagomiscuentasFile([
            'company_id' => 1,
            'date' => '31-01-2019',
            'type' => PagomiscuentasFile::TYPE_PAYMENT
        ]);
        $model->save();

        expect('Create payment when type is payment only', $model->createPayment(45900, (new \DateTime('now'))->format('Y-m-d'), 123, 'description', 1))->true();
    }

    public function testCreateRelationWithPayment()
    {
        $model = new PagomiscuentasFile([
            'company_id' => 1,
            'from_date' => '01-01-2019',
            'date' => '31-01-2019',
            'type' => PagomiscuentasFile::TYPE_PAYMENT
        ]);
        $model->save();

        $payment = $model->createPayment(45900, (new \DateTime('now'))->format('Y-m-d'), 123, 'description', 1);

        expect('Create relation when type is payment', $model->createRelationWithPayment($payment->payment_id))->true();

    }

    public function testCloseExport()
    {
        $date_from = (new \DateTime('now'))->format('d-m-Y');
        $date = (new \DateTime('now'))->modify('+1 month')->format('d-m-Y');
        $model = new PagomiscuentasFile([
            'company_id' => 1,
            'from_date' => $date_from,
            'date' => $date,
            'type' => PagomiscuentasFile::TYPE_BILL
        ]);
        $model->save();

        $bill = BillExpert::createBill(1);
        $bill->company_id = 1;
        $bill->status = 'closed';
        $bill->partner_distribution_model_id = 1;
        $bill->save();
        $model->close();

        expect('Model status is closed', $model->status )-> equals(PagomiscuentasFile::STATUS_CLOSED);

        $relation = PagomiscuentasFileHasBill::find()->where(['pagomiscuentas_file_id' => $model->pagomiscuentas_file_id])->andWhere(['bill_id' => $bill->bill_id   ])->one();

        expect('Relation was build', count($relation) > 0)->true();
        expect('Relation bill is correct', $relation->bill_id)->equals($bill->bill_id);
    }
}