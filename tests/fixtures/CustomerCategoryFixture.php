<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 22/02/19
 * Time: 12:26
 */

namespace app\tests\fixtures;


use app\modules\sale\models\CustomerCategory;
use yii\test\ActiveFixture;

class CustomerCategoryFixture extends ActiveFixture
{

    public $modelClass= CustomerCategory::class;
}