<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 20/03/19
 * Time: 11:00
 */

use app\modules\ticket\models\SchemaHasStatus;
use app\tests\fixtures\TicketStatusFixture;
use app\modules\ticket\models\Schema;

class SchemaHasStatusTest extends \Codeception\Test\Unit
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
        $model = new SchemaHasStatus();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $schema = new Schema([
           'name' => 'Schema',
           'class' => self::class
        ]);
        $schema->save();

        $model = new SchemaHasStatus([
            'schema_id' => $schema->schema_id,
            'status_id' => 1,
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new SchemaHasStatus();
        expect('Invalid when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $schema = new Schema([
            'name' => 'Schema',
            'class' => self::class
        ]);
        $schema->save();

        $model = new SchemaHasStatus([
            'schema_id' => $schema->schema_id,
            'status_id' => 1,
        ]);

        expect('Valid when full and new', $model->save())->true();
    }
}