<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 15/07/19
 * Time: 11:45
 */

namespace app\modules\westnet\api\components;


use app\modules\westnet\api\models\User;

class Controller extends \yii\rest\Controller
{

    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
            // performs authorization by token
            'tokenAuth' => [
                'class' => \conquer\oauth2\TokenAuth::class,
                'identityClass' => User::class
            ],
        ]);
    }


}