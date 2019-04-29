<?php

use app\modules\zone\models\Zone;

class ZoneTest extends \Codeception\Test\Unit {

    /**
     * @var \CodeGuy
     */
    protected $guy;

    protected function _before() {
        
    }

    protected function _after() {
        
    }

    public function testNewZoneIsInvalid() {
        $zone = new Zone();
        $this->assertFalse($zone->validate());
    }

    public function testZoneIsValid() {
        $zone = new Zone();
        
        $lipsum = new joshtronic\LoremIpsum();
        
        $zone->name = $lipsum->words(2);
        $zone->type = $lipsum->words(1);
        $zone->status = Zone::STATUS_ENABLED;
        
        $this->assertTrue($zone->validate());
    }

    public function testNotSaveZoneWhenIsInvalid() {
        $zone = new Zone();

        expect('Nolt save when invalid', $zone->save())->false();
    }

    public function testsaveZoneIWhensValid() {
        $zone = new Zone();

        $lipsum = new joshtronic\LoremIpsum();

        $zone->name = $lipsum->words(2);
        $zone->type = $lipsum->words(1);
        $zone->status = Zone::STATUS_ENABLED;

        expect('Save when is valid', $zone->save())->true();
    }

    public function testGetTabName()
    {
        $model = new Zone([
            'name' => 'Zona padre',
            'type' => 'Pais',
            'status' => Zone::STATUS_ENABLED
        ]);
        $model->save();

        $model2 = new Zone([
            'name' => 'Zona hija',
            'type' => 'Pais',
            'status' => Zone::STATUS_ENABLED,
            'parent_id' => $model->zone_id
        ]);
        $model2->save();

        $converted = strtr($model->getTabName(), array_flip(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES)));

        expect('Parent tab ir right', $model->getTabName())->equals('Zona padre');
        expect('Parent tab ir right', htmlentities($converted))->equals('Zona padre');

        $converted = strtr($model2->getTabName(), array_flip(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES)));

        expect('Parent tab ir right', htmlentities($converted))->equals('&nbsp;&nbsp;&nbsp;&nbsp;Zona hija');
    }

    public function testGetFullZone()
    {
        $model = new Zone([
            'name' => 'Zona padre',
            'type' => 'Pais',
            'status' => Zone::STATUS_ENABLED
        ]);
        $model->save();

        $model2 = new Zone([
            'name' => 'Zona hija',
            'type' => 'Pais',
            'status' => Zone::STATUS_ENABLED,
            'parent_id' => $model->zone_id
        ]);
        $model2->save();

        expect('Get full name parent', $model->getFullZone($model->zone_id))->equals('Zona padre');
        expect('Get full name parent', $model2->getFullZone($model2->zone_id))->equals('Zona hija, Zona padre');
    }

    public function testGetForSelect()
    {
        $model = new Zone([
            'name' => 'Zona padre',
            'type' => 'Pais',
            'status' => Zone::STATUS_ENABLED
        ]);
        $model->save();

        $model2 = new Zone([
            'name' => 'Zona hija',
            'type' => 'Pais',
            'status' => Zone::STATUS_ENABLED,
            'parent_id' => $model->zone_id
        ]);
        $model2->save();

        expect('Get for select', count(Zone::getForSelect()))->equals(2);

        $model3 = new Zone([
            'name' => 'Zona hija 2',
            'type' => 'Pais',
            'status' => Zone::STATUS_ENABLED,
            'parent_id' => $model->zone_id
        ]);
        $model3->save();

        expect('Get for select', count(Zone::getForSelect()))->equals(3);
    }

    public function testSearchByName()
    {
        $model = new Zone([
            'name' => 'Zona padre',
            'type' => 'Pais',
            'status' => Zone::STATUS_ENABLED
        ]);
        $model->save();

        $search = Zone::searchByName('Zona');

        expect('Find zone', $search)->notEmpty();
        expect('Find zone by name', $search[0]->name)->equals('Zona padre');

    }
}
