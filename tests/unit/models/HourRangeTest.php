<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 29/03/19
 * Time: 19:16
 */

use app\modules\sale\models\HourRange;

class HourRangeTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testInvalidWhenNewAndEmpty ()
    {
        $model = new HourRange();

        expect('Invalid when new and empty', $model->validate())->false();
    }

    public function testValidWhenNewAndFull ()
    {
        $model = new HourRange([
            'from' => '13:30',
            'to' => '20:50',
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenNewAndEmpty ()
    {
        $model = new HourRange();

        expect('Not save when new and empty', $model->save())->false();
    }

    public function testSaveWhenNewAndFull ()
    {
        $model = new HourRange([
            'from' => '13:30',
            'to' => '20:50',
        ]);

        expect('Save when full and new', $model->save())->true();
    }

    public function testGetFullName()
    {
        $model = new HourRange([
            'from' => '13:30',
            'to' => '20:50',
        ]);

        expect('Get full name is right', $model->getFullName())->equals('13:30-20:50');
    }

    public function testGetHourRangeForChecklist()
    {
        $hour_ranges = HourRange::getHourRangeForCheckList();

        expect('For checklist has first', array_key_exists('12:50-20:50', $hour_ranges));
        expect('For checklist has second', array_key_exists('08:30-12:00', $hour_ranges));
    }
}