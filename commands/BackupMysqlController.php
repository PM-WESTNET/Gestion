<?php

namespace app\commands;

class BackupMysqlController extends \yii\console\Controller
{


    public function actionInitBackup($init)
    {
        $backup = new \app\modules\backup\models\Backup();
        $backup->status = 'in_process';
        $backup->init_timestamp = $init;

        $backup->save();

        return;
    }

    public function actionFinishErrorBackup($init, $log ,$init_log)
    {
        $backup = \app\modules\backup\models\Backup::findOne([
            'init_timestamp' => strtotime(\Yii::$app->formatter->asDatetime($init, 'yyyy-MM-dd HH:mm:ss')),
            'status' => 'in_process'
        ]);

        if ($backup) {
            $backup->status = 'error';
            $backup->finish_timestamp = date('d-m-Y');

            $fileLog= file($log);

            $description = '';

            for ($i = $init_log; $i === (count($fileLog)-1); $i++){
               $description .= $fileLog[$i]. PHP_EOL;
            }

            $backup->description = $description;

            $backup->save();


        }

        return '';
    }

    public function actionFinishSuccessBackup($init)
    {
        $backup = \app\modules\backup\models\Backup::findOne([
            'init_timestamp' => strtotime(\Yii::$app->formatter->asDatetime($init, 'yyyy-MM-dd HH:mm:ss')),
            'status' => 'in_process'
        ]);

        if ($backup) {
            $backup->status = 'success';
            $backup->finish_timestamp = date('d-m-Y');

            $backup->save();
        }
    }

}

