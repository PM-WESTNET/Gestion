<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 12/12/18
 * Time: 12:15
 */

use app\modules\sale\models\Address;
use app\modules\zone\models\Zone;
use app\tests\fixtures\ZoneFixture;
use app\modules\sale\models\Category;
use yii\helpers\ArrayHelper;

class CategoryTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _before(){}

    public function _fixtures()
    {}

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new Category();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenEmptyAndNew()
    {
        $model = new Category([
            'name' => 'Planes de Internet Residencial'
        ]);

        expect('Valid when full and new', $model->save())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new Category();
        expect('Not save when empty and new', $model->save())->false();
    }

    public function testSaveWhenEmptyAndNew()
    {
        $model = new Category([
            'name' => 'Planes de Internet Residencial'
        ]);

        expect('Save when full and new', $model->save())->true();
    }

    public function testGetOrderedcategories()
    {
        $model1 = new Category([
            'name' => 'Planes de Internet Residencial'
        ]);
        $model1->save();

        $model2 = new Category([
            'name' => 'Planes de Internet Residencial Especial',
            'parent_id' => $model1->category_id
        ]);
        $model2->save();

        $categories = ArrayHelper::map(Category::getOrderedCategories(),'category_id','name');

        expect('Get ordered categories', $categories[$model1->category_id])->equals('Planes de Internet Residencial');
        expect('Get ordered categories', $categories[$model2->category_id])->equals('Planes de Internet Residencial Especial');
    }

    public function testTabName()
    {
        $model1 = new Category([
            'name' => 'Planes de Internet Residencial'
        ]);
        $model1->save();

        $model2 = new Category([
            'name' => 'Planes de Internet Residencial Especial',
            'parent_id' => $model1->category_id
        ]);
        $model2->save();

        $converted = strtr($model2->getTabName(), array_flip(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES)));

        expect('Tab Name parent', $model1->tabName)->equals('Planes de Internet Residencial');
        expect('Tab Name', htmlentities($converted))->equals('&nbsp;&nbsp;&nbsp;&nbsp;Planes de Internet Residencial Especial');
    }
}