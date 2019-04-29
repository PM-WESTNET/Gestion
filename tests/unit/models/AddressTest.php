<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 12/12/18
 * Time: 12:15
 */

use app\modules\sale\models\Address;
use app\tests\fixtures\ZoneFixture;

class AddressTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _before(){}

    public function _fixtures()
    {
        return [
            'zone' => [
                'class' => ZoneFixture::class
            ]
        ];
    }

    // tests
    public function testValidWhenEmptyAndNew()
    {
        $model = new Address();
        expect('Invalid when empty and new', $model->validate())->true();
    }

    public function testSaveWhenEmptyAndNew()
    {
        $model = new Address();

        expect('Saved when full and new', $model->save())->true();
    }

    //TODO
    public function testIndentName(){}

    public function testGetFullAddress()
    {
        $model = new Address([
            'zone_id' => 2
        ]);
        expect('Get full address', $model->getFullAddress())->equals(' Mendoza, Argentina');

        $model->street = 'Calle 1';
        expect('Get full address with street', $model->getFullAddress())->equals('Calle 1 S/N Mendoza, Argentina');

        $model->number = '123';
        expect('Get full address with street and number', $model->getFullAddress())->equals('Calle 1 123, Mendoza, Argentina');

        $model->between_street_1 = 'Calle 2';
        $model->between_street_2 = 'Calle 3';
        expect('Get full address with street, number, between_street1-2', $model->getFullAddress())->equals('Calle 1 123, entre Calle 2 y Calle 3,  Mendoza, Argentina');

        $model->block = 'Block1';
        expect('Get full address with street, number, between_street1-2, block', $model->getFullAddress())->equals('Calle 1 123, entre Calle 2 y Calle 3,  Mendoza, Argentina, M-Block1');

        $model->house = '10';
        expect('Get full address with street, number, between_street1-2, block, house', $model->getFullAddress())->equals('Calle 1 123, entre Calle 2 y Calle 3,  Mendoza, Argentina, M-Block1 C-10');

        $model->tower = '1';
        expect('Get full address with street, number, between_street1-2, block, house, tower', $model->getFullAddress())->equals('Calle 1 123, entre Calle 2 y Calle 3,  Mendoza, Argentina, M-Block1 C-10 T-1');

        $model->floor = '8';
        expect('Get full address with street, number, between_street1-2, block, house, tower, floor', $model->getFullAddress())->equals('Calle 1 123, entre Calle 2 y Calle 3,  Mendoza, Argentina, M-Block1 C-10 T-1 P-8');

        $model->department = '3';
        expect('Get full address with street, number, between_street1-2, block, house, tower, floor, department', $model->getFullAddress())->equals('Calle 1 123, entre Calle 2 y Calle 3,  Mendoza, Argentina, M-Block1 C-10 T-1 P-8 D-3');

        $model->indications = 'indications 1';
        expect('Get full address with street, number, between_street1-2, block, house, tower, floor, department, indications', $model->getFullAddress())->equals('Calle 1 123, entre Calle 2 y Calle 3,  Mendoza, Argentina, M-Block1 C-10 T-1 P-8 D-3 (indications 1)');
    }

    public function testGetShortAddress()
    {
        $model = new Address([
            'zone_id' => 2
        ]);
        expect('Get full address', $model->getFullAddress())->equals(' Mendoza, Argentina');

        $model->street = 'Calle 1';
        $model->number = '123';
        expect('Get full address with street and number', $model->getFullAddress())->equals('Calle 1 123, Mendoza, Argentina');

        $model->block = 'Block1';
        expect('Get full address with street, number, block', $model->getFullAddress())->equals('Calle 1 123, Mendoza, Argentina, M-Block1');

        $model->house = '10';
        expect('Get full address with street, number, block, house', $model->getFullAddress())->equals('Calle 1 123, Mendoza, Argentina, M-Block1 C-10');

        $model->tower = '1';
        expect('Get full address with street, number, block, house, tower', $model->getFullAddress())->equals('Calle 1 123, Mendoza, Argentina, M-Block1 C-10 T-1');

        $model->floor = '8';
        expect('Get full address with street, number, block, house, tower, floor', $model->getFullAddress())->equals('Calle 1 123, Mendoza, Argentina, M-Block1 C-10 T-1 P-8');

        $model->department = '3';
        expect('Get full address with street, number,  block, house, tower, floor, department', $model->getFullAddress())->equals('Calle 1 123, Mendoza, Argentina, M-Block1 C-10 T-1 P-8 D-3');
    }

    public function testIsEqual()
    {
        $model1 = new Address([
            'zone_id' => 2,
            'street' => 'Calle 1',
            'number' => '123',
            'block' => 'Block1',
            'house' => '10',
            'tower' => '1',
            'floor' => '8',
            'department' => '3'
        ]);

        $model2 = new Address([
            'zone_id' => 2,
            'street' => 'Calle 1',
            'number' => '123',
            'block' => 'Block1',
            'house' => '10',
            'tower' => '1',
            'floor' => '8',
            'department' => '3'
        ]);

        $model3 = new Address([
            'zone_id' => 2,
            'street' => 'Calle 1',
            'number' => '132',
            'block' => 'Block1',
            'house' => '10',
            'tower' => '1',
            'floor' => '8',
            'department' => '3'
        ]);

        expect('Compare two equal addresses', $model1->isEqual($model2))->true();
        expect('Compare two non equal addresses', $model1->isEqual($model3))->false();
    }
}