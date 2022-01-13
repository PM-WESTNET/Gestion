<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use Yii;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($message = 'hello world')
    {
        echo $message . "\n";
    }

    public function actionPotentialCustomers(){
        $this->stdout("Comienzo\n");
        // groups all customers ID in the log table and then uses those IDs to search all changes to IP4_1 of connection
        $customer_logs = Yii::$app->db->createCommand("SELECT (
                                                        SELECT cl.customer_log_id FROM customer_log cl
                                                        WHERE cl.customer_id = cus_log.customer_id AND 
                                                        cl.action = 'Actualizacion de Datos de Conexion: Ip4 1'
                                                        ORDER BY cl.customer_log_id DESC
                                                        LIMIT 1
                                                        ) as customer_log_id
                                                    FROM customer_log cus_log WHERE customer_log_id IS NOT NULL 
                                                    GROUP BY cus_log.customer_id")
                                                    ->queryAll();
        foreach ($customer_logs as $log_id) {
            if(!empty($log_id)){
                $customer_log = Yii::$app->db->createCommand("select cl.customer_id, cl.new_value 
                                                from customer_log cl
                                                where cl.customer_log_id = :customer_log_id")
                                                ->bindValue('customer_log_id',$log_id['customer_log_id'])
                                                ->queryOne();
                
                $this->stdout("Cliente: ".$customer_log['customer_id']."\n");
                

                $contract = Yii::$app->db->createCommand("select cont.contract_id from contract cont
                                                where cont.customer_id = :customer_id
                                                order by cont.contract_id desc
                                                limit 1")
                                                ->bindValue('customer_id',$customer_log['customer_id'])
                                                ->queryOne();
                
                if(!empty($contract)){
                    $connection = Yii::$app->db->createCommand("select conn.connection_id from connection conn
                                                where conn.contract_id = :contract_id")
                                                ->bindValue('contract_id',$contract['contract_id'])
                                                ->queryOne();

                    $ipRepeats = Yii::$app->db->createCommand("select conn.connection_id from connection conn
                                                where conn.connection_id != :connection_id
                                                and conn.ip4_1 = :ip4_1_old")
                                                ->bindValue('connection_id',$connection['connection_id'])
                                                ->bindValue('ip4_1_old',ip2long($customer_log['new_value']))
                                                ->queryAll();

                    $this->stdout("Conexion: ".$connection['connection_id']."\n");

                    if(empty($ipRepeats)){
                        Yii::$app->db->createCommand("UPDATE connection SET ip4_1_old = :ip4_1_old WHERE connection_id = :connection_id")
                                ->bindValue('ip4_1_old', ip2long($customer_log['new_value']))
                                ->bindValue('connection_id',$connection['connection_id'])
                                ->execute();
                        $this->stdout("No se repite: \n");   
                    }else{
                        Yii::$app->db->createCommand("UPDATE connection SET ip4_1_old = :ip4_1_old WHERE connection_id = :connection_id")
                                ->bindValue('ip4_1_old', null)
                                ->bindValue('connection_id',$connection['connection_id'])
                                ->execute();
                        $this->stdout("Si se repite: \n");
                    }
                    
                    
                }
                
            }

        }
                                    
        return false;
    }
}
