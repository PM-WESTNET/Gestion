<?php
/**
 * Created by PhpStorm.
 * User: dexterlab10
 * Date: 18/06/19
 * Time: 16:19
 */

use app\modules\sale\models\DocumentType;
use Codeception\Test\Unit;

class DocumentTypeTest extends Unit
{

    public function testInvalidWhenEmptyAndNew()
    {
        $model = new DocumentType();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new DocumentType([
            'name' => 'documentType',
            'code' => '123'
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new DocumentType();
        expect('Invalid when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new DocumentType([
            'name' => 'documentType',
            'code' => '123'
        ]);

        expect('Valid when full and new', $model->save())->true();
    }
}