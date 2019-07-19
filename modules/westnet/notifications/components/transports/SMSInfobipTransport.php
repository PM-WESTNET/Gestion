<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 24/05/19
 * Time: 13:03
 */

namespace app\modules\westnet\notifications\components\transports;


use app\components\helpers\EmptyLogger;
use app\components\helpers\Inflector;
use app\modules\config\models\Config;
use app\modules\sale\models\Company;
use app\modules\sale\modules\contract\models\Plan;
use PHPExcel;
use PHPExcel_Cell_DataType;
use PHPExcel_IOFactory;
use Yii;

class SMSInfobipTransport implements TransportInterface
{

    public function features()
    {
        return [
            'manual' => true
        ];
    }

    public function send($notification)
    {
        $destinataries = $notification->destinataries;
        $messages = [];

        foreach ($destinataries as $destinatary) {
            $customers = $destinatary->getCustomersQuery();

            foreach ($customers->all() as $customer) {
                $phones = $this->getPhones($customer);
                $destinations = array_map(function($phone) use ($notification){
                    return [
                        'to' => $phone,
                        'messageId' => $notification->notification_id . uniqid()
                    ];
                }, $phones);

                $messages[] = [
                    "from" => 'Westnet',
                    "destinations" => $destinations,
                    "text" => $this->replaceText($notification->content, $customer),
                    "flash" => false,
                    "language" => [
                        "languageCode" => "ES"
                    ],
                    "transliteration" => "SPANISH",
                    "intermediateReport" => true,
                    "notifyUrl" => "https://www.example.com/sms/advanced",
                    "notifyContentType" => "application/json",
                    "callbackData" => "DLR callback data",
                ];
            }
        }

        $response = InfobipService::sendMultipleSMS($messages);

        if ($response) {
            return [
                'status' => 'success'
            ];
        }

        return [
            'status' => 'error',
        ];
    }

    /**
     * @param $notification
     */
    public function export($notification)
    {
        //Para evitar que la memoria alcance el limite
        Yii::setLogger(new EmptyLogger());

        //Nombre de archivo
        try{
            $fileName = 'sms-contacts.csv';

            ob_start();
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment;filename="'.$fileName.'"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header ('Cache-Control: cache, must-revalidate');
            header ('Pragma: public');

            $resource = fopen('php://output', 'w');

            $header = [
                 Yii::t('app', 'Name'),
                 Yii::t('app', 'Phone1'),
                 Yii::t('app', 'Phone2'),
                 Yii::t('app', 'Code'),
                 Yii::t('app', 'Payment Code'),
                 Yii::t('app', 'Node ID'),
                 Yii::t('app', 'Balance'),
                 Yii::t('app', 'Exp Datetime'),
                 Yii::t('app', 'Company'),
                 Yii::t('app', 'Debt Bills'),
                 Yii::t('app', 'Status'),
                 Yii::t('app', 'Category'),
                 Yii::t('app', 'Plan'),
                 Yii::t('app', 'Valor futuro del plan'),
            ];

            fputcsv($resource, $header, ";");

            $i = 2;
            foreach($notification->destinataries as $destinataries){
                /** @var Query $query */
                $query = $destinataries->getCustomersQuery(false);
                foreach($query->each() as $customer) {
                    $phones = [];
                    $p1 = trim(preg_replace('/[?&%$() \/-][A-Za-z]*/', '', $customer['phone']));
                    $p2 = trim(preg_replace('/[?&%$() \/-][A-Za-z]*/', '', $customer['phone2']));
                    $p3 = trim(preg_replace('/[?&%$() \/-][A-Za-z]*/', '', $customer['phone3']));
                    $p4 = trim(preg_replace('/[?&%$() \/-][A-Za-z]*/', '', $customer['phone4']));

                    if(strlen($p1) > 7 ) {
                        $phones[] = (string)$p1;
                    }

                    if(strlen($p2) > 7 && $p1 != $p2 ) {
                        $phones[] = (string)$p2;
                    }

                    if(strlen($p3) > 7 && $p3 != $p1 && $p3 != $p2 ) {
                        $phones[] = (string)$p3;
                    }

                    if(strlen($p4) > 7 && $p4 != $p1 && $p4 != $p2  && $p4 != $p3) {
                        $phones[] = (string)$p4;
                    }

                    foreach($phones as $phone) {
                        $plan = Plan::findOne($customer['plan']);
                        $future_price = $plan ? $plan->futureFinalPrice : '';
                        $company = Company::findOne($customer['customer_company']);
                        $line = [
                            $customer['name'],
                            $phone,
                            $customer['code'],
                            (isset($customer['node']) ? $customer['node'] : ''),
                            (isset($customer['saldo']) ? $customer['saldo'] : ''),
                            '',
                            ($company ? $company->code : $customer['company_code']),
                            (isset($customer['debt_bills']) ? $customer['debt_bills'] : '' ),
                            Yii::t('westnet', ucfirst($customer['status'])),
                            $customer['category'],
                            ($plan ? $plan->name : ''),
                            ($future_price ? Yii::$app->formatter->asCurrency($future_price) : '')
                        ];
                        fputcsv($resource, $line, ';');

                    }
                }

            }

           fclose($resource);

        }catch (\Exception $ex){
            error_log($ex->getMessage());
        }

    }

    private function getPhones($customer){
        $phones = [];

        $p1 = trim(preg_replace('/[?&%$() \/-][A-Za-z]*/', '', $customer['phone']));
        $p2 = trim(preg_replace('/[?&%$() \/-][A-Za-z]*/', '', $customer['phone2']));
        $p3 = trim(preg_replace('/[?&%$() \/-][A-Za-z]*/', '', $customer['phone3']));

        if(strlen($p1) > 7 ) {
            $phones[] = '549'.(string)$p1;
        }

        if(strlen($p2) > 7 && $p1 != $p2 ) {
            $phones[] = '549'.(string)$p2;
        }

        if(strlen($p3) > 7 && $p3 != $p1 && $p3 != $p2 ) {
            $phones[] = '549'.(string)$p3;
        }

        return $phones;
    }

    private function replaceText($text, $customer)
    {
        $replaced_text = $text;

        $replace_max_string = self::getMaxLengthReplacement();
        $replaced_text = str_replace('@Nombre', trim(substr($customer['name'], 0, $replace_max_string['@Nombre'])), $replaced_text);
        $replaced_text = str_replace('@Telefono1', substr($customer['phone'], 0, $replace_max_string['@Telefono1']), $replaced_text);
        $replaced_text = str_replace('@Telefono2', substr($customer['phone'], 0, $replace_max_string['@Telefono2']), $replaced_text);
        $replaced_text = str_replace('@Codigo', substr($customer['code'], 0, $replace_max_string['@Codigo']), $replaced_text);
        $replaced_text = str_replace('@CodigoDePago', substr($customer['payment_code'], 0, $replace_max_string['@CodigoDePago']), $replaced_text);
        $replaced_text = str_replace('@Nodo', substr($customer['node'], 0, $replace_max_string['@Nodo']), $replaced_text);
        $replaced_text = str_replace('@Saldo', substr($customer['saldo'], 0, $replace_max_string['@Saldo']), $replaced_text);
        $replaced_text = str_replace('@CodigoEmpresa', substr($customer['company_code'], 0, $replace_max_string['@CodigoEmpresa']), $replaced_text);
        $replaced_text = str_replace('@FacturasAdeudadas', substr($customer['debt_bills'], 0, $replace_max_string['@FacturasAdeudadas']), $replaced_text);
        $replaced_text = str_replace('@Estado', Yii::t('westnet', ucfirst($customer['status'])), $replaced_text);
        $replaced_text = str_replace('@Categoria', substr($customer['category'], 0, $replace_max_string['@Categoria']), $replaced_text);

        $inflector = new \yii\helpers\Inflector();

        return $inflector->transliterate($replaced_text, \yii\helpers\Inflector::TRANSLITERATE_MEDIUM);

    }

    /**
     * @return array
     * devuelve el largo máximo que debe tener cada uno de los reemplazos
     */
    public static function getMaxLengthReplacement()
    {
        return [
            '@Nombre' => Config::getValue('notification-replace-@Nombre'),
            '@Telefono1' => Config::getValue('notification-replace-@Telefono1'),
            '@Telefono2' => Config::getValue('notification-replace-@Telefono2'),
            '@Codigo' => Config::getValue('notification-replace-@Codigo'),
            '@CodigoDePago' => Config::getValue('notification-replace-@CodigoDePago'),
            '@Nodo' => Config::getValue('notification-replace-@Nodo'),
            '@Saldo' => Config::getValue('notification-replace-@Saldo'),
            '@CodigoEmpresa' => Config::getValue('notification-replace-@CodigoEmpresa'),
            '@FacturasAdeudadas' => Config::getValue('notification-replace-@FacturasAdeudadas'),
            '@Categoria' => Config::getValue('notification-replace-@Categoria'),
        ];
    }

}