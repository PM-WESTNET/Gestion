<?php

use app\modules\cobrodigital\models\PaymentCardFile;
use Codeception\Test\Unit;

class PaymentCardFileTest extends Unit {
    /**
     * @var \UnitTester
     */
    protected $tester;

    // tests

    public function testValid()
    {
        $model = new PaymentCardFile([
            'path' => 'uploads/cobrodigital/file5cdaeb116db1b.csv',
            'status' => PaymentCardFile::STATUS_DRAFT,
            'upload_date' => (new \DateTime('now'))->format('Y-m-d'),
            'file_name' => 'file.name'
        ]);

        expect('Valid', $model->validate())->true();
    }

    public function testSave()
    {
        $model = new PaymentCardFile([
            'path' => 'uploads/cobrodigital/file5cdaeb116db1b.csv',
            'status' => PaymentCardFile::STATUS_DRAFT,
            'upload_date' => (new \DateTime('now'))->format('Y-m-d'),
            'file_name' => 'file.name'
        ]);
        expect('Saved', $model->save())->true();
    }
}