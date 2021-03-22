<?php namespace models;

use app\modules\westnet\models\IpRange;

class IpRangeTest extends \Codeception\Test\Unit
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

    public function testValidateInsertScenarioSuccess()
    {
        $model = new IpRange([
            'ip_start' => '10.1.0.1',
            'ip_end' => '10.1.0.254',
            'node_id' => 1,
            'type' => IpRange::NODE_SUBNET_TYPE,
            'status' => 'enabled'
        ]);

        expect('Error on save', $model->validate())->true();
    }

    public function testValidateNetInsertScenarioSuccess()
    {
        $model = new IpRange([
            'net_address' => '172.10.0.0/24',
            'type' => IpRange::NET_TYPE,
            'status' => 'enabled'
        ]);

        expect('Error on save', $model->validate())->true();
    }

    public function testSaveNodeRangeSuccess()
    {
        $model = new IpRange([
            'ip_start' => '10.1.0.1',
            'ip_end' => '10.1.0.254',
            'node_id' => 1,
            'type' => IpRange::NODE_SUBNET_TYPE,
            'status' => 'enabled'
        ]);

        expect('Error on save', $model->save())->true();
    }

    public function testSaveNetSuccess()
    {
        $model = new IpRange([
            'net_address' => '172.10.0.0/24',
            'type' => IpRange::NET_TYPE,
            'status' => 'enabled'
        ]);

        expect('Error on save', $model->save())->true();
    }

    public function testCalculateRange()
    {
        $model = new IpRange([
            'net_address' => '172.10.0.0/24',
            'type' => IpRange::NET_TYPE,
            'status' => 'enabled'
        ]);

        $model->calculateIpRange();

        expect('Range fail', $model->ip_start)->equals(ip2long('172.10.0.3'));
        expect('Range fail', $model->ip_end)->equals(ip2long('172.10.0.254'));

    }

}