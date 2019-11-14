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

    /**
     * Reemplaza el texto con los datos del cliente.
     */
    public function replaceText($text, $customer)
    {
        $replaced_text = $text;

        $replace_max_string = SMSIntegratechTransport::getMaxLengthReplacement();
        $replaced_text = str_replace('@Nombre', trim(substr($customer['name'], 0, $replace_max_string['@Nombre'])), $replaced_text);
        $replaced_text = str_replace('@CodigoDeCliente', trim(substr($customer['code'], 0, $replace_max_string['@CodigoDeCliente'])), $replaced_text);
        $replaced_text = str_replace('@Telefono1', substr($customer['phone'], 0, $replace_max_string['@Telefono1']), $replaced_text);
        $replaced_text = str_replace('@Telefono2', substr($customer['phone2'], 0, $replace_max_string['@Telefono2']), $replaced_text);
        $replaced_text = str_replace('@Code', substr($customer['code'], 0, $replace_max_string['@Codigo']), $replaced_text);
        $replaced_text = str_replace('@PaymentCode', substr($customer['payment_code'], 0, $replace_max_string['@CodigoDePago']), $replaced_text);
        $replaced_text = str_replace('@Nodo', substr($customer['node'], 0, $replace_max_string['@Nodo']), $replaced_text);
        $replaced_text = str_replace('@Saldo', substr($customer['saldo'], 0, $replace_max_string['@Saldo']), $replaced_text);
        $replaced_text = str_replace('@CompanyCode', substr($customer['company_code'], 0, $replace_max_string['@CodigoEmpresa']), $replaced_text);
        $replaced_text = str_replace('@FacturasAdeudadas', substr($customer['debt_bills'], 0, $replace_max_string['@FacturasAdeudadas']), $replaced_text);
        $replaced_text = str_replace('@Estado', Yii::t('westnet', ucfirst($customer['status'])), $replaced_text);
        $replaced_text = str_replace('@Categoria', substr($customer['category'], 0, $replace_max_string['@Categoria']), $replaced_text);

        return $replaced_text;

    }
}
