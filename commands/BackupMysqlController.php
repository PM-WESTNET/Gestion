<?php

namespace app\commands;

use DateTime;
use \Yii;
use app\modules\backup\models\Backup;
use app\modules\alertsbot\controllers\TelegramController;

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
            //            for ($i = $init_log; $i === (count($fileLog)-1); $i++){
            //               $description .= $fileLog[$i]. PHP_EOL;
            //            }
            //
            //            $backup->description = $description;

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
            //todo:send message to telegram bot
            return; 
        }

        $params = Yii::$app->params['backups'];
        $dir = $params['dirbase'];
        $name = 'full_backup.zip';
        $fileOut = '/home/backups/percona/'. $name;
        $host = $params['host'];
        $user = $params['user'];
        $pass = $params['pass'];
        if(!isset($params['remoteBaseDir'])) return;
        if(!isset($params['perconaFullDir'])) return;

        $command1 = "sudo innobackupex --host=$host --user=$user --password=$pass --no-timestamp  --no-lock $dir";
        $command2 = "sudo zip -r $fileOut $dir";

        $result = shell_exec($command1);
        $result2 = shell_exec($command2);

        if ($result ==  '' && file_exists($fileOut)) {
            try {

                if($this->transferToRemoteServer($fileOut, $params['perconaFullDir']. $date->format('Y-m-d_H-i'). '.tar')) {
                    $backup->status = 'success';
                }else {
                    $backup->description = 'Backup Realizado localmente. No se pudo transferir a servidor de backups';
                    $backup->status = 'error';
                    
                }
                $backup->save();
                return;
            }catch(\Exception $e) {
                $backup->status = 'error';
                $backup->description = 'Backup Realizado localmente. No se pudo transferir a servidor de backups ' . $e->getMessage();
                $backup->save();
                // send error to telegram
                TelegramController::sendProcessCrashMessage('**** Cronjob Error Catch: backup-mysql/percona-full-back ****', $e);
                return;
            }
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
        $dirInc = $params['dirincremental']. '/';
        $name = 'incremental.zip';
        $fileOut = '/home/backups/percona/'.  $name;
        $dirIncBefore = $params['dirincremental'];
        $host = $params['host'];
        $user = $params['user'];
        $pass = $params['pass'];
        if(!isset($params['perconaIncrementalDir'])) return;

        $command = "sudo innobackupex --incremental --host=$host --user=$user --password=$pass  --no-timestamp  --no-lock --incremental-basedir=$dir $dirIncBefore ";
        $command2 = "sudo zip -r $fileOut $dirInc";
        if (!file_exists($dirInc)) {
            mkdir($dirInc, 0777);
        }

        $result = shell_exec($command);
        $result2 = shell_exec($command2);

        if ($result ==  '' && file_exists($fileOut)) {
            try {
                if($this->transferToRemoteServer($fileOut, $params['perconaIncrementalDir'].$date->format('Y-m-d_H-i'). '.zip')) {
                    $backup->status = 'success';
                }else {
                    $backup->description = 'Backup Realizado localmente. No se pudo transferir a servidor de backups';
                    $backup->status = 'error';
                    
                }
                $backup->save();
                return;
            }catch(\Exception $e) {
                $backup->status = 'error';
                $backup->description =$e->getMessage();
                $backup->save();
                // send error to telegram
                TelegramController::sendProcessCrashMessage('**** Cronjob Error Catch: backup-mysql/percona-incremental-back ****', $e);
                return;
            }
        }

        $backup->status = 'error';
        $backup->description ='Error desconocido';
        $backup->save();
        return;
    }

    public function actionMysqlBackup()
    {
        try{
            $params = Yii::$app->params['backups'];
            $host = $params['host'];
            $user = $params['user'];
            $pass = $params['pass'];
            $date = (new DateTime('now'));
            if(!isset($params['mysqlDir'])) return;

            if (isset($params['databases'])) {
                foreach ($params['databases'] as $db) {
                    // create backup register for db
                    $backup = new Backup();
                    // start timestamp
                    $backup->init_timestamp = $date->format('d-m-Y H:i:s');
                    $backup->status = 'in_process';
                    $backup->database = $db;


                    // backup name, path to save to, and command to excecute
                    $name = $db.'.sql';
                    $fileOutput = $params['backupMysqlDir'] .'/'. $name;
                    $command = "mysqldump -h$host -u$user -p$pass $db > $fileOutput";
                    // create a backup and save it locally on $fileOutput path
                    $result = shell_exec($command);


                    // finish timestamp
                    $backup->finish_timestamp = (new DateTime('now'))->format('d-m-Y H:i:s');
                    // save temporal backup attributes on DB before trying to send them to the storage server
                    $backup->save();

                    if ($result ==  '' && file_exists($fileOutput)) {
                        try {
                            if($this->transferToRemoteServer($fileOutput, $params['mysqlDir']. $db.'_'. $date->format('dmY_His').'.sql')) {
                                $backup->status = 'success';

                            }else {
                                $backup->description = Yii::t('app', 'Backup saved locally. Could not transfer to backup server');
                                $backup->status = 'error';
                                
                                throw new \Exception( $backup->description );
                            }
                            $backup->save();
                        }catch(\Exception $e) {
                            $backup->status = 'error';
                            $backup->description = Yii::t('app', 'Backup saved locally. Could not transfer to backup server') . ' ' . $e->getMessage();
                            $backup->save();

                            TelegramController::sendProcessCrashMessage('**** Cronjob Error Catch: backup-mysql/mysql-backup ****', $e);
                        }
                    }else {
                        $backup->status = 'error';
                        $backup->description = Yii::t('app', 'Backup file not found') . ' - ' . $name;
                        $backup->save();

                        throw new \Exception( $backup->description );
                    }
                }
            }
        }
        catch(\Exception $e) {

            TelegramController::sendProcessCrashMessage('**** Cronjob Error Catch: backup-mysql/mysql-backup ****', $e);
        }
    }

    /**
     * Verifica el espacio disponible en el disco y devuelve verdadero si el espacio es mayor al m??nimo establecido en params
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
        // connect to remote server
        $connection = $this->connectToRemoterServer();

        // default case: something went wrong
        $return_val = false;

        // if connected and space is availible, send sql via ssh2_scp_send
        if($connection !== false && $this->verifyRemoteSpace($connection)) {
            if(ssh2_scp_send($connection, $filename, $remoteFile, 0777)){
                // finally, close ssh connection. this theorically prevents truncated/incompleted file transfers  
                ssh2_exec($connection, 'exit');
                
                $return_val = true;
            }
        }

        return $return_val;
    }

    private function verifyRemoteSpace($connection){
        $params = Yii::$app->params['backups'];
        if(!isset($params['remoteBaseDir'])) return false;
        $remoteBaseDir = $params['remoteBaseDir'];
        $command = "df -k $remoteBaseDir | tr -s ' ' | cut -d' ' -f 4 | tr -dc '0-9'";
        $stream = ssh2_exec($connection, $command);

        if ($stream !== false) {
            if ((float)$stream > $params['remoteDiskSpace']) {
                return true;
            }
        }

        return false;
    }


}