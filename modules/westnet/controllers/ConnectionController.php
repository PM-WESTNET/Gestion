<?php

namespace app\modules\westnet\controllers;

use app\modules\sale\modules\contract\models\Contract;
use app\modules\westnet\models\Connection;
use Yii;
use app\modules\westnet\models\IpRange;
use app\modules\westnet\models\search\IpRangeSearch;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\config\models\Config;
use app\modules\westnet\models\PaymentExtensionHistory;
use app\modules\sale\models\Customer;
use yii\db\Query;

/**
 * Class ConnectionController
 * @package app\modules\westnet\controllers
 */
class ConnectionController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Enable the connection
     * @return mixed
     */
    public function actionEnable($id)
    {
        Yii::$app->response->format = 'json';
        $model = $this->findModel($id);

        $model->status_account = Connection::STATUS_ACCOUNT_ENABLED;
        $model->due_date = null;
        $model->update(false);

        $result = (($model->status_account == Connection::STATUS_ACCOUNT_ENABLED) || ($model->status == Connection::STATUS_ENABLED));

        return [
            'status' => ($result ? 'success' : 'error')
        ];
    }

    /**
     * Disable de connection
     * @return mixed
     */
    public function actionDisable($id)
    {
        Yii::$app->response->format = 'json';
        $model = $this->findModel($id);
        $model->status_account = Connection::STATUS_ACCOUNT_DISABLED;
        $model->due_date = null;
        $model->update(false);

        $result = ($model->status_account == Connection::STATUS_ACCOUNT_DISABLED);

        return [
            'status' => ($result ? 'success' : 'error')
        ];
    }

    /**
     * Force the connection
     * @return mixed
     */
    public function actionForce($id)
    {
        Yii::$app->response->format = 'json';
        /** @var Connection $model */
        $model = $this->findModel($id);
        $result = true;
        if(Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            $create_pti= $data['create_product'] === 'true' ? 1 : 0;
            if ($model->canForce()) {
                if ($model->force($data['due_date'], $data['product_id'], $data['vendor_id'], $create_pti)) {
                    // had this piece of code inside the ->force() function before, but was triggering cause of APP and IVR when forcing connections from other scripts--
                    $payment_extension_product = Config::getValue('extend_payment_product_id'); // this dynamically gets the product ID from DB
                    if ($data['product_id'] == $payment_extension_product) { // in case the product is payment-extension
                        PaymentExtensionHistory::createPaymentExtensionHistory($model->contract->customer_id, PaymentExtensionHistory::FROM_MANUALLY); // it creates an entry for PaymentExtensionHistory (correlated to the product detail created inside ->force())
                    }
                    return [
                        'status' => 'success'
                    ];
                }
            }else{
                return [
                    'status' =>'error',
                    'message' =>  Yii::t('westnet', 'Can`t force this connection becouse this connection has exceeded the limit forced in the month ')
                ];
            }

            return [
                'status' => 'error',
                'message' => Yii::t('app','Can`t force this connection')
            ];
        }
    }


    /**
     * Finds the IpRange model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return IpRange the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Connection::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionUpdateOnMikrotik($connection_id){
        $conn = Connection::findOne($connection_id);
        //triggers the aftersave of Connection model which has mikrotik connection update.
        if($conn->save()){
            //saved
        }else{
            //failed to save
        }
        return $this->redirect(['/sale/contract/contract/view', 'id' => $conn->contract_id]);
    }

    /**
     * trigger url /index.php?r=westnet%2Fconnection%2Fupdate-all-onu-serial-numbers
     * 
     * ONU Serial number massive charge to all customers based on a JSON obj input.
     * 
     * example..
     * {
     *  "Código cliente": 72969,
     *  "SN": "TPLGF7FCA479"
     * }, ...
     */
    public function actionUpdateAllOnuSerialNumbers(){
        echo("--start--\n");
        // set time limit to unlimited cause it can take more than 40 minutes to complete
        set_time_limit(0);

        $logPath = "../modules/westnet/onu_sn_seeds/migrations.txt";

        // get contentes of seeds from the file that should be populated in production of each company at the moment of execution
        $json = file_get_contents("../modules/westnet/onu_sn_seeds/migration_onu_serial_numbers.txt",true);
        if(empty($json)){
            echo("--empty json--\n");
            return false;
        } 
        
        // create an array based on file contents . code => sn
        $customer_onu_array = array_column((json_decode($json,true)),'sn','codigo_cliente');


        // init header line for seeding
        $content = "\nMigration started at ".date('d/m/y H:i:s')."\n";
        // open migration .log to write any errors
        file_put_contents($logPath, $content,FILE_APPEND);
        
        $i = 0;
        foreach($customer_onu_array as $code => $onu_serial){
            // skip empty ONU serials
            if(!empty($onu_serial)){
                $select_query = "select conn.onu_sn,conn.connection_id
                    from customer cus
                    left join contract cont on cont.customer_id = cus.customer_id
                    left join connection conn on conn.contract_id = cont.contract_id
                    where cont.status = 'active'
                    and cus.code = :code
                    order by conn.connection_id desc
                    limit 1";
                $connection_data = Yii::$app->db->createCommand($select_query)
                    ->bindValue('code', $code)
                    ->queryOne();
                $onu_sn_old = $connection_data['onu_sn'];
                $connection_id = $connection_data['connection_id'];
                // var_dump($code,$connection_data);
                // die();

                $query = Yii::$app->db->createCommand(
                    "update connection c
                    set c.onu_sn = :onu_serial
                    where c.connection_id = ( 
                        select Conn2.connection_id from (
                            select connection_id
                            from customer cus
                            left join contract cont on cont.customer_id = cus.customer_id
                            left join connection conn on conn.contract_id = cont.contract_id
                            where cont.status = 'active'
                            and cus.code = :code
                            order by conn.connection_id desc
                            limit 1
                        ) as Conn2
                    )")
                    ->bindValue('code', $code)
                    ->bindValue('onu_serial', $onu_serial)
                    ;
                try {
                    // throws new \Exception in case of failure
                    $query->execute();
                    
                    $content = "\n".date('d/m/y H:i:s')." - $i - UPDATE SUCCESS - ".$code." (code) - CONNECTION_ID: $connection_id - ONU's SN from: $onu_sn_old to: ".$onu_serial;
                } catch (\Exception $ex) {
                    // echo 'Query failed';
                    $content = "\n".date('d/m/y H:i:s')." - $i - UPDATE ERROR - customer: ".$code." (code)  couldn't update ONU's SN\n".$ex->getMessage();
                }
            }
            else{
                $content = "\n".date('d/m/y H:i:s')." - $i - SKIPPED - ".$code." (code) - ERR. EMPTY ONU SERIAL";
            }
            
            file_put_contents($logPath, $content,FILE_APPEND);
            $i++;
        }
        // init header line for seeding
        $content = "\nMigration end at ".date('d/m/y H:i:s')."\n";
        file_put_contents($logPath, $content,FILE_APPEND);
        echo('end');
        return true;
    }
    
}