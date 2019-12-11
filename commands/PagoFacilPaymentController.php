<?php
/**
 * Created by PhpStorm.
 * User: dexterlab10
 * Date: 06/11/19
 * Time: 16:49
 */
namespace app\commands;

use app\modules\checkout\models\PagoFacilTransmitionFile;
use app\modules\westnet\notifications\components\transports\CPortalTransport;
use app\modules\westnet\notifications\models\Notification;
use app\modules\checkout\models\Payment;
use app\modules\sale\models\Bill;
use app\modules\sale\models\Customer;
use app\modules\sale\models\CustomerLog;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\sale\modules\contract\models\ContractDetail;
use app\modules\sale\modules\contract\models\Plan;
use app\modules\sale\modules\contract\models\ProgrammedPlanChange;
use app\modules\westnet\notifications\models\Transport;
use yii\console\Controller;
use app\modules\checkout\models\search\PaymentSearch;
use Yii;

/**
 * Este comando se encarga de tratar todos los procesos largos que tengan que ver con pagos de pago facil
 */
class PagoFacilPaymentController extends Controller
{

    public function actionClosePagoFacilPayments()
    {
        $pending_pago_facil_files = PagoFacilTransmitionFile::find()->where(['status' => PagoFacilTransmitionFile::STATUS_PENDING])->all();
        $pending_pago_facil_files_qty = count($pending_pago_facil_files);

        echo "Hay $pending_pago_facil_files_qty archivos para ser procesados\n";

        if(Yii::$app->mutex->acquire('pago_facil_mutex') && $pending_pago_facil_files) {
            echo "Iniciando proceso\n";
            $i = 0;

            foreach ($pending_pago_facil_files as $file) {
                echo "$file->pago_facil_transmition_file_id\n";

                //Solo se tienen en cuenta los pagos que están pendientes de cerrarse.
                $payments = $file->getCheckoutPayments()->where(['payment.status' => 'draft'])->all();

                echo "Pagos pendientes de cerrar de archivo id $file->pago_facil_transmition_file_id: ". count($payments) ."\n";

                Yii::$app->cache->set('close_pago_facil_payments', [
                    'total' => count($payments),
                    'qty' => $i
                ]);

                foreach ($payments as $payment) {
                    if($payment->close()){
                        $i++;
                        Yii::$app->cache->set('close_pago_facil_payments', [
                            'total' => count($payments),
                            'qty' => $i
                        ]);
                    }
                }

                $file->updateAttributes(['status' => PagoFacilTransmitionFile::STATUS_CLOSED]);
                echo "Archivo $file->pago_facil_transmition_file_id procesado con éxito";
            }
        } else {
            echo "Proceso actualmente corriendo\n";
        }
    }
}