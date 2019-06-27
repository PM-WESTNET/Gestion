<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 12/03/19
 * Time: 17:30
 */

namespace app\tests\fixtures;

use yii\test\ActiveFixture;
use app\modules\westnet\models\Vendor;

class VendorFixture extends ActiveFixture
{

    public $modelClass = Vendor::class;

    public $depends = [
        DocumentTypeFixture::class,
        AddressFixture::class,
        AccountFixture::class,
        VendorCommissionFixture::class,
        ProviderFixture::class
    ];
}