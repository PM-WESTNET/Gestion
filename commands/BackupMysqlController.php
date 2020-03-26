<?php

namespace app\commands;

use DateTime;
use \Yii;
use app\modules\backup\models\Backup;

class BackupMysqlController extends \yii\console\Controller
{


    public function actionInitBackup($db)
    {
        $backup = new \app\modules\backup\models\Backup();
        $backup->status = 'in_process';
        $backup->init_timestamp = (new \DateTime('now'))->format('d-m-Y H:i:s');
        $backup->database = $db;

        $backup->save();

        return;
    }

    public function actionFinishBackup($db, $log )
    {
        $backup = \app\modules\backup\models\Backup::findOne([
            'database' => $db,
            'status' => 'in_process'
        ]);

        if ($backup) {
            if (is_file($log)){
                $backup->status = 'success';
            }else {
                $backup->status = 'error';
            }

            $backup->finish_timestamp = (new \DateTime('now'))->format('d-m-Y H:i:s');

//            $fileLog= file($log);
//
//            $description = '';
//
////            for ($i = $init_log; $i === (count($fileLog)-1); $i++){
////               $description .= $fileLog[$i]. PHP_EOL;
////            }
////
////            $backup->description = $description;

            $backup->save();

        }

        return '';
    }

    public function actionPerconaFullBack()
    {
        
        $date = (new DateTime('now'));
        
        $backup = new Backup();
        $backup->init_timestamp = $date->format('d-m-Y H:i:s');
        $backup->status = 'in_process';
        $backup->save();
        
        if(!isset(Yii::$app->params['backups']) || (isset(Yii::$app->params['backups']) && empty(Yii::$app->params['backups']))) {
            
        }

        if (!$this->verifySpace()) {
            $backup->status = 'error';
            $backup->description ='Falta Espacio en disco';
            $backup->save();
            return; 
        }

        $params = Yii::$app->params['backups'];
        $dir = $params['dirbase'];
        $fileOut = $dir. $date->format('Y-m-d_H-i'). '.tar';
        $host = $params['host'];
        $user = $params['user'];
        $pass = $params['pass'];

        $command = "sudo innobackupex --host=$host --user=$user --password=$pass --stream=tar $dir > $fileOut";

        $result = shell_exec($command);

        if ($result ==  '' && file_exists($fileOut)) {
            $backup->status = 'success';
            $backup->save();
            return;
        }

        $backup->status = 'error';
        $backup->description ='Error desconocido';
        $backup->save();
        return;

    }

    public function actionPerconaIncrementalBack()
    {
        $date = (new DateTime('now'));
        
        $backup = new Backup();
        $backup->init_timestamp = $date->format('d-m-Y H:i:s');
        $backup->status = 'in_process';
        $backup->save();
        
        if(!isset(Yii::$app->params['backups']) || (isset(Yii::$app->params['backups']) && empty(Yii::$app->params['backups']))) {
            
        }

        if (!$this->verifySpace()) {
            $backup->status = 'error';
            $backup->description ='Falta Espacio en disco';
            $backup->save();
            return; 
        }

        $params = Yii::$app->params['backups'];
        $dir = $params['dirbase'];
        $dirInc = $params['dirincremental']. '/'.$date->format('Y-m-d'). '/';
        $fileOut = $dirInc. $date->format('Y-m-d_H-i'). '.tar';
        $dirIncBefore = $params['dirincremental']. '/'.$date->modify('-1 day')->format('Y-m-d');
        $host = $params['host'];
        $user = $params['user'];
        $pass = $params['pass'];

        $command = "sudo innobackupex --incremental --host=$host --user=$user --password=$pass --stream=tar --incremental-basedir=$dir $dirIncBefore > $fileOut";

        if (!file_exist($dirInc)) {
            mkdir($dirInc, 0777);
        }

        $result = shell_exec($command);

        if ($result ==  '' && file_exists($fileOut)) {
            $backup->status = 'success';
            $backup->save();
            return;
        }

        $backup->status = 'error';
        $backup->description ='Error desconocido';
        $backup->save();
        return;
    }

    public function actionMysqlBackup()
    {
        $params = Yii::$app->params['backups'];
        $date = (new DateTime('now'));
        $host = $params['host'];
        $user = $params['user'];
        $pass = $params['pass'];

        if (isset($params['databases'])) {
            foreach ($params['databases'] as $db) {
                $backup = new Backup();
                $backup->init_timestamp = $date->format('d-m-Y H:i:s');
                $backup->status = 'in_process';
                $backup->database = $db;
                $backup->save();

                $fileOutput = $params['backupMysqlDir'] .'/'. $db.'_'. $date->format('dmY_His').'.sql';
                $command = "mysqldump -h $host -u $user -p $pass $db > $fileOutput";
                $result = shell_exec($command);

                if ($result ==  '' && file_exists($fileOutput)) {
                    $backup->status = 'success';
                    $backup->save();
                }else {
                    $backup->status = 'error';
                    $backup->description ='Error desconocido';
                    $backup->save();
                }
            }
        }
    }

    /**
     * Verifica el espacio disponible en el disco y devuelve verdadero si el espacio es mayor al mínimo establecido en params
     */
    private function verifySpace() {
        $limit = Yii::$app->params['backups']['min_disk_space'];

        $space = ((disk_free_space('/')/1024)/1024);

        if ($space !== false && $space > $limit) {
            return true;
        }

        return false;
    }


}

