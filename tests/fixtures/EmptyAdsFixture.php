<?php
/**
 * Created by PhpStorm.
 * User: quoma
 * Date: 21/02/19
 * Time: 10:06
 */

namespace app\tests\fixtures;

use app\modules\westnet\models\EmptyAds;
use yii\test\ActiveFixture;

class EmptyAdsFixture extends ActiveFixture
{
    public $modelClass = EmptyAds::class;

    public $depends = [
        PaymentCardFixture::class,
        CompanyFixture::class,
        NodeFixture::class
    ];
}