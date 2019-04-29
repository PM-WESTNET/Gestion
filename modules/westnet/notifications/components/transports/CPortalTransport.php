<?php

namespace app\modules\westnet\notifications\components\transports;

use Yii;
use yii\base\Component;
use app\modules\westnet\mesa\components\models\Notificacion;
use app\components\helpers\EmptyLogger;
use app\modules\westnet\notifications\components\helpers\LayoutHelper;

/**
 * Description of CPortalTransport
 *
 * @author mmoyano
 */
class CPortalTransport implements TransportInterface{
    
    public function features()
    {
        return [
            'manualSent',
            'programmable',
            'manyTimesPerDay'
        ];
    }
    
    public function export($notification){
        
        //Para evitar que la memoria alcance el limite
        Yii::setLogger(new EmptyLogger());
        
        //Nombre de archivo
        $fileName = 'ips.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        
        $output = fopen('php://output', 'w');
        
        //Encabezado:
        fputcsv($output, [ Yii::t('app', 'Name'), Yii::t('app', 'Lastname'), Yii::t('app', 'IP') ] );
        
        foreach($notification->destinataries as $destinataries){
            $query = $destinataries->getCustomersQuery(true);
            foreach($query->each() as $customer) {
                if($customer['ipv4']){
                    fputcsv($output, [ '"'. trim($customer['name']).'"', '"'.trim($customer['lastname']).'"', '"'.long2ip($customer['ipv4']).'"'], ",", " ");
                }
            }
        }

    }
    
    public function send($notification){
        
        $dto = new Notificacion();

        $dto->id = $notification->notification_id;
        $dto->titulo = $notification->subject;
        $dto->desde = $notification->from_date;
        $dto->hasta = $notification->to_date;
        $dto->horas = $notification->calcDailyPeriod();
        $dto->filtros = [];
        $dto->ips = [];
        
        //Obtenemos la lista de ips
        foreach($notification->destinataries as $destinataries){
            $dto->ips = array_merge($dto->ips, $destinataries->getIps());
        }
        //Removemos las claves de tipo string
        $dto->ips = array_values($dto->ips);
        
        $content = Yii::$app->view->render('@app/modules/westnet/notifications/body/content/content', ['notification' => $notification]);
        $content = Yii::$app->view->render(LayoutHelper::getLayoutAlias($notification->layout), ['content' => $content]);

        $dto->texto = $content;

        $mesa = \app\modules\config\models\Config::getValue('mesa_server_address');

        $req = new \app\modules\westnet\mesa\components\request\NotificacionRequest($mesa);
        $res = $req->create($dto);
        
        if($res == true){
            return [
                'status' => 'success'
            ];
        }else{
            return [
                'status' => 'error',
                'error' => $req->error
            ];
        }
    }
}
