<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 20/03/19
 * Time: 10:39
 */

use app\modules\ticket\models\Schema;
use app\tests\fixtures\TicketStatusFixture;
use app\modules\ticket\models\Status;
use app\modules\ticket\models\SchemaHasStatus;

class SchemaTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _fixtures(){
        return [
            'status' => [
                'class' => TicketStatusFixture::class
            ],
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new Schema();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new Schema([
            'name' => 'Schema',
            'class' => self::class,
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new Schema();
        expect('Invalid when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new Schema([
            'name' => 'Schema',
            'class' => self::class,
        ]);

        expect('Valid when full and new', $model->save())->true();
    }

    public function testGetForSelect()
    {
        $model = new Schema([
            'name' => 'Schema',
            'class' => self::class,
        ]);
        $model->save();
        $select = Schema::getForSelect();

        expect('Get for select', $select)->notEmpty();
        expect('Get for select', count($select))->equals(1);
        expect('Has model', $select[$model->schema_id])->equals('Schema');
    }

    public function testGetStatusesForSelect()
    {
        $select = Schema::getStatusesForSelect();

        expect('Get for select', $select[1])->equals('nuevo');
        expect('Get for select', $select[2])->equals('en curso (asignado]');
        expect('Get for select', $select[3])->equals('en curso (planificado]');
        expect('Get for select', $select[4])->equals('en espera');
        expect('Get for select', $select[5])->equals('cerrado (resuelto]');
        expect('Get for select', $select[6])->equals('cerrado (no resuelto]');
        expect('Get for select', count($select))->equals(6);
    }

    public function testGetStatusesBySchema()
    {
        $model = new Schema([
            'name' => 'Schema',
            'class' => self::class,
        ]);
        $model->save();

        $relation = new SchemaHasStatus([
            'schema_id' => $model->schema_id,
            'status_id' => 1
        ]);
        $relation->save();

        expect('Get status by schema is not empty', $model->getStatusesBySchema()[0])->notEmpty();
        expect('Get status by schema has id key', array_key_exists('id',$model->getStatusesBySchema()[0]))->true();
        expect('Get status by schema has name key', array_key_exists('name',$model->getStatusesBySchema()[0]))->true();
        expect('Get status by schema has 1 in id', $model->getStatusesBySchema()[0]['id'])->equals(1);
        expect('Get status by schema has nuevo in name', $model->getStatusesBySchema()[0]['name'])->equals('nuevo');
    }
}