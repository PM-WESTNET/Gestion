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
        $name = $date->format('Y-m-d_H-i'). '.tar';
        $fileOut = $dir. $name;
        $host = $params['host'];
        $user = $params['user'];
        $pass = $params['pass'];

        $command = "sudo innobackupex --host=$host --user=$user --password=$pass --stream=tar $dir > $fileOut";

        $result = shell_exec($command);

        if ($result ==  '' && file_exists($fileOut)) {
            if($this->transferToRemoteServer($name, '/home/gestion/backups/percona/full/'. $date->format('Y-m-d'). '/'. $name)) {
                $backup->status = 'success';
            }else {
                $backup->description = 'Backup Realizado localmente. No se pudo transferir a servidor de backups';
                $backup->status = 'error';
                
            }
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
        $name = $date->format('Y-m-d_H-i'). '.tar';
        $fileOut = $dirInc. $name;
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
            if($this->transferToRemoteServer($name, '/home/gestion/backups/percona/incremental/'. $date->format('Y-m-d'). '/' . $name)) {
                $backup->status = 'success';
            }else {
                $backup->description = 'Backup Realizado localmente. No se pudo transferir a servidor de backups';
                $backup->status = 'error';
                
            }
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

                $name = $db.'_'. $date->format('dmY_His').'.sql';
                $fileOutput = $params['backupMysqlDir'] .'/'. $name;
                $command = "mysqldump -h $host -u $user -p $pass $db > $fileOutput";
                $result = shell_exec($command);

                if ($result ==  '' && file_exists($fileOutput)) {
                    if($this->transferToRemoteServer($name, '/home/gestion/backups/mysql/'. $name)) {
                        $backup->status = 'success';
                    }else {
                        $backup->description = 'Backup Realizado localmente. No se pudo transferir a servidor de backups';
                        $backup->status = 'error';
                        
                    }
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

    private function connectToRemoterServer() {
        $params = Yii::$app->params['backups'];

        $connection = ssh2_connect($params['remote_server_url'], 22);

        if (ssh2_auth_password($connection, $params['remote_server_user'], $params['remote_server_password'])){
            return $connection;
        }

        return false;
    }

    private function transferToRemoteServer($filename, $remoteFile) {
        $params = Yii::$app->params['backups'];

        $connection = $this->connectToRemoterServer();

        if($connection !== false && $this->verifyRemoteSpace($connection)) {
            if(ssh2_scp_send($connection, $filename, $remoteFile, 0777)){
                return true;
            }
        }

        return false;

    }

    private function verifyRemoteSpace($connection){
        $params = Yii::$app->params['backups'];
        $command = "df -k /mnt/pruebaRaid | tr -s ' ' | cut -d' ' -f 4 | tr -dc '0-9'";
        $stream = ssh2_exec($connection, $command);

        if ($stream !== false) {
            if ((float)$stream > $params['remoteDiskSpace']) {
                return true;
            }
        }

        return false;
    }


}

