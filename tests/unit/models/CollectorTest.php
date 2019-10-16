<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 10/01/19
 * Time: 10:08
 */

use app\modules\westnet\ecopagos\models\Collector;
use app\modules\westnet\ecopagos\models\Assignation;

class CollectorTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $ecopago_id;

    public function _fixtures(){
        return [
            'ecopago' => [
                'class' => \app\tests\fixtures\EcopagoFixture::class,
            ]
        ];
    }

    public function testInvalidWhenEmptyAndNew()
    {
        $model = new Collector();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new Collector([
            'name' => 'Pepe',
            'lastname' => 'Hongo',
            'number' => '123',
            'document_number' => '12456789',
            'document_type' => 'DNI'
        ]);
        $model->validate();
        \Codeception\Util\Debug::debug($model->getErrors());

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new Collector();
        expect('Invalid when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new Collector([
            'name' => 'Pepe',
            'lastname' => 'Hongo',
            'number' => '1234',
            'document_number' => '12456789',
            'document_type' => 'DNI',
            'password' => 'password',
            'password_repeat' => 'password'
        ]);

        $model->save();

        expect('Valid when full and new', $model->save())->true();
    }

    public function testFetchDocumentTypes()
    {
        $document_types = (new Collector())->fetchDocumentTypes();

        expect('Fetch document types', array_key_exists( 'DNI',$document_types));
    }

    public function testFetchEcopagos()
    {
        $model = new Collector([
            'name' => 'Pepe',
            'lastname' => 'Hongo',
            'number' => '123456',
            'document_number' => '12456789',
            'document_type' => 'DNI',
            'password' => 'password',
            'password_repeat' => 'password'
        ]);
        $model->save();

        $assignation = new Assignation([
            'ecopago_id' => 1,      //Fixture
            'collector_id' => $model->collector_id,
            'date' => (new DateTime('now'))->format('Y-m-d'),
            'time' => (new DateTime('now'))->format('H:m:i'),
            'datetime' =>(new DateTime('now'))->getTimestamp()
        ]);
        $assignation->save();

        $ecopagos = $model->fetchEcopagos();

        \Codeception\Util\Debug::debug($ecopagos);

        expect('Fetch document types', $ecopagos[1])->equals('Ecopago1');
    }

    public function testFetchCollectorsAsArray()
    {
        $model = new Collector([
            'name' => 'Juan',
            'lastname' => 'Gallegos',
            'number' => '123456',
            'document_number' => '12456789',
            'document_type' => 'DNI',
            'password' => 'password',
            'password_repeat' => 'password'
        ]);
        $model->save();

        $model2 = new Collector([
            'name' => 'Jose',
            'lastname' => 'Gallegos',
            'number' => '1234567',
            'document_number' => '12456789',
            'document_type' => 'DNI',
            'password' => 'password',
            'password_repeat' => 'password'
        ]);
        $model2->save();

        $collectors = Collector::fetchCollectorsAsArray();

        expect('Fetch collectors as array Juan', $collectors[$model->collector_id])->equals('Juan Gallegos (123456)');
        expect('Fetch collectors as array', $collectors[$model2->collector_id])->equals('Jose Gallegos (1234567)');
    }

    public function testIsFromEcopago()
    {
        $model = new Collector([
            'name' => 'Juan',
            'lastname' => 'Gallegos',
            'number' => '123456',
            'document_number' => '12456789',
            'document_type' => 'DNI',
            'password' => 'password',
            'password_repeat' => 'password'
        ]);
        $model->save();

        expect('Is not from ecopago ', $model->isFromEcopago(1))->false();

        $assignation = new Assignation([
            'ecopago_id' => 1,      //Fixture
            'collector_id' => $model->collector_id,
            'date' => (new DateTime('now'))->format('Y-m-d'),
            'time' => (new DateTime('now'))->format('H:m:i'),
            'datetime' =>(new DateTime('now'))->getTimestamp()
        ]);
        $assignation->save();

        expect('Is from ecopago', $model->isFromEcopago(1))->true();
    }
}