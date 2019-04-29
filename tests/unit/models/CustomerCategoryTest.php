<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 12/12/18
 * Time: 12:15
 */

use app\modules\sale\models\CustomerCategory;
use yii\helpers\ArrayHelper;

class CustomerCategoryTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new CustomerCategory();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new CustomerCategory([
            'name' => 'Familia',
            'status' => 'enabled'
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new CustomerCategory();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new CustomerCategory([
            'name' => 'Familia',
            'status' => 'enabled'
        ]);

        expect('Saved when full and new', $model->save())->true();
    }

    public function testGetOrderedCustomerCategories()
    {
        $model1 = new CustomerCategory([
            'name' => 'Familia',
            'status' => 'enabled'
        ]);
        $model1->save();

        $model2 = new CustomerCategory([
            'name' => 'Familia Hijo',
            'status' => 'enabled',
            'parent_id' => $model1->customer_category_id
        ]);
        $model2->save();

        $categories = ArrayHelper::map(CustomerCategory::getOrderedCustomerCategories(),'customer_category_id','name');

        expect('Get ordered categories', $categories[$model1->customer_category_id])->equals('Familia');
        expect('Get ordered categories', $categories[$model2->customer_category_id])->equals('Familia Hijo');
    }

    public function testGetTabName()
    {
        $model1 = new CustomerCategory([
            'name' => 'Familia',
            'status' => 'enabled'
        ]);
        $model1->save();

        $model2 = new CustomerCategory([
            'name' => 'Familia Hijo',
            'status' => 'enabled',
            'parent_id' => $model1->customer_category_id
        ]);
        $model2->save();

        $converted = strtr($model2->getTabName(), array_flip(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES)));

        expect('Get tab name', $model1->getTabName())->equals('Familia');
        expect('Get tab name',  htmlentities($converted))->equals('&nbsp;&nbsp;&nbsp;&nbsp;Familia Hijo');
    }
}