<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 10/01/19
 * Time: 10:08
 */

use app\modules\westnet\ecopagos\models\Ecopago;
use app\tests\fixtures\CollectorFixture;
use app\tests\fixtures\ProviderFixture;
use app\tests\fixtures\AccountFixture;
use app\tests\fixtures\StatusFixture;
use app\modules\westnet\ecopagos\models\Assignation;

class EcopagoTest extends \Codeception\Test\Unit
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
            'status' => [
                'class' => StatusFixture::class,
            ],
            'account' => [
                'class' => AccountFixture::class
            ],
            'provider_id' => [
                'class' => ProviderFixture::class
            ],
            'collector' => [
                'class' => CollectorFixture::class
            ],
            'ecopago' => [
                'class' => \app\tests\fixtures\EcopagoFixture::class
            ]
        ];
    }

    public function testInvalidWhenEmptyAndNew()
    {
        $model = new Ecopago();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new Ecopago([
            'status_id' => 1,   //Fixture
            'name' => 'Ecopago1',
            'commission_type' =>  'percentaje',
            'commission_value' => '1.6',
            'account_id' => 1,  //Fixture
            'provider_id' => 149
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new Ecopago();
        expect('Invalid when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new Ecopago([
            'status_id' => 1,   //Fixture
            'name' => 'Ecopago1',
            'commission_type' =>  'percentage',
            'commission_value' => '1.6',
            'account_id' => 1,  //Fixture
            'provider_id' => 149
        ]);

        expect('Valid when full and new', $model->save())->true();
    }

    public function testFetchCollectors()
    {
        $model = new Ecopago([
            'status_id' => 1,           //Fixture
            'name' => 'Ecopago1',
            'commission_type' =>  'percentage',
            'commission_value' => '1.6',
            'account_id' => 1,          //Fixture
            'provider_id' => 149        //Fixture
        ]);
        $model->save();

        $assignation = new Assignation([
            'ecopago_id' => $model->ecopago_id,
            'collector_id' => 1,        //Fixture
            'date' => (new DateTime('now'))->format('Y-m-d'),
            'time' => (new DateTime('now'))->format('H:m:i'),
            'datetime' =>(new DateTime('now'))->getTimestamp()
        ]);
        $assignation->save();

        expect('Fetch collectors empty', $model->fetchCollectors()[1])->equals('Alejandro Videla (16576)');
        expect('Fetch collectors empty', $model->fetchCollectors(false))->equals('Alejandro Videla (16576)');
    }

    public function testFetchCashiers()
    {
        $model = new Ecopago([
            'status_id' => 1,           //Fixture
            'name' => 'Ecopago1',
            'commission_type' =>  'percentage',
            'commission_value' => '1.6',
            'account_id' => 1,          //Fixture
            'provider_id' => 149        //Fixture
        ]);
        $model->save();

        //TODO Test y fixture de cashiers
    }


    public function testFailDisableEcopago()
    {
        $model = Ecopago::findOne(2);

        expect('Failed', $model->disable())->false();
    }

    public function testSuccessDisableEcopago()
    {
        $model = Ecopago::findOne(1);

        expect('Failed', $model->disable())->true();
    }
}

    /*

    public function fetchCashiers($asArray = true) {
        $cashiers = [];

        if (!empty($this->cashiers)) {

            foreach ($this->cashiers as $cashier) {
                $cashiers[$cashier->cashier_id] = $cashier->name . ' ' . $cashier->lastname;
            }
        }

        if ($asArray)
            return $cashiers;
        else
            return implode(', ', $cashiers);
    }


    public function createCommission() {
        $commission = new Commission;
        $commission->type = $this->commission_type;
        $commission->value = $this->commission_value;
        $commission->create_datetime = time();
        $commission->ecopago_id = $this->ecopago_id;
        $commission->save();
    }


    public function isNearLimit() {

        $payoutsAmount = $this->fetchValidPayouts();

        if (!empty($payoutsAmount)) {

            $limit = $this->limit;
            $currentAmount = $payoutsAmount;
            $difference = $limit - $currentAmount;

            $percentage = 100 - (($difference * 100) / $limit);

            if ($percentage >= 85)
                return true;

            return false;
        } else
            return false;
    }


    public function isOnLimit() {

        $payoutsAmount = $this->fetchValidPayouts();

        if (empty($payoutsAmount) || $payoutsAmount <= $this->limit) {
            return true;
        } else
            return false;
    }


    private function fetchValidPayouts() {

        //$lastSunday = date('d/m/Y', strtotime('last Sunday', strtotime(date('Y-m-d'))));
        //$nextSunday = date('d/m/Y', strtotime('next Sunday', strtotime(date('Y-m-d'))));
        return Payout::find()->where([
            'ecopago_id' => $this->ecopago_id,
        ])
            ->andWhere(['<>', 'status', Payout::STATUS_CLOSED_BY_BATCH])
            ->andWhere(['<>', 'status', Payout::STATUS_REVERSED])
            //->andWhere(['>=', 'date', $lastSunday])
            //->andWhere(['<', 'date', $nextSunday])
            ->sum('amount');
    }
     */

