<?php

namespace app\commands;

use app\modules\alertsbot\controllers\TelegramController;

class LoadAverageController extends \yii\web\Controller
{
    public $enableCsrfValidation = false;

    public function actionLoadAverageAlert()
    {

        try{

            $loadAverage = sys_getloadavg();
            $la = $loadAverage[2];
    
            TelegramController::actionMessageSysLoadAvg($la);
    
        }catch(\Exception $ex){
            TelegramController::sendProcessCrashMessage('**** Cronjob Error Catch: load-average/load-average-alert ****', $ex);
        }

    }
}
