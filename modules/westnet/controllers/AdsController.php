<?php

namespace app\modules\westnet\controllers;

use app\components\helpers\PDFService;
use app\components\web\Controller;
use app\modules\config\models\Config;
use app\modules\sale\components\CodeGenerator\CodeGeneratorFactory;
use app\modules\sale\models\Company;
use app\modules\sale\models\Customer;
use app\modules\sale\models\Product;
use app\modules\sale\models\ProductPrice;
use app\modules\sale\modules\contract\components\CompanyByNode;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\sale\modules\contract\models\Plan;
use app\modules\westnet\models\Connection;
use app\modules\westnet\models\EmptyAds;
use app\modules\westnet\models\IpRange;
use app\modules\westnet\models\Node;
use Hackzilla\BarcodeBundle\Utility\Barcode;
use Yii;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use app\components\pdf\PdfUtils;
use Da\QrCode\QrCode;
use kartik\mpdf\Pdf;
use Picqer\Barcode\BarcodeGeneratorPNG;


/**
 * IpRangeController implements the CRUD actions for IpRange model.
 */
class AdsController extends Controller {
    
    public $freeAccessActions = ['barcode'];

    public function behaviors() {
        return array_merge(parent::behaviors(), [
        ]);
    }

    /**
     * Lists all IpRange models.
     * @return mixed
     */
    public function actionPrint($id, $node_id) {

        $model = $this->findModel($id);
        $contract = Contract::findOne(['contract_id' => $id]);
        $node = Node::findOne(['node_id' => $node_id]);

        $company = $contract->customer->parentCompany;

        $codes = [];

        $customer = $model->customer;
        $customer->company_id = $node->company_id;

        $generator = CodeGeneratorFactory::getInstance()->getGenerator('PagoFacilCodeGenerator');
        $code = str_pad($company->code, 4, "0", STR_PAD_LEFT) . ($company->code == '9999' ? '' : '000' ) .
                str_pad($customer->code, 5, "0", STR_PAD_LEFT) ;

        $payment_code= $generator->generate($code);
        $customer->payment_code= $payment_code;
        $customer->updateAttributes(['payment_code', 'company_id']);
            
        if ($company->code !== '9999') {
            $codes[] = ['payment_code' =>$payment_code, 'code' => $model->customer->code];
        }else{
            $codes[]= ['payment_code' =>$payment_code, 'code' => $model->customer->code];
        }

        $this->layout = '//pdf';

        $plans = $this->getPlans($customer->parent_company_id, $customer->customerCategory->name);

        $view = $this->render('pdf', [
            'model'     => $model,
            'codes'     => $codes,
            'node'      => $node,
            'company'   => $company,
            'plans'     => $plans
        ]);
        $model->print_ads= 1;
        $model->updateAttributes(['print_ads']);
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_RAW;
        $response->headers->set('Content-type: application/pdf');
        $response->setDownloadHeaders('ads.pdf', 'application/pdf', true);
        
        return PDFService::makePdf($view);
    }

    public function actionPrintEmptyAds($company_id = null, $node_id = null, $qty = null)
    {

        if ($node_id !== null && $qty !== null && $company_id !== null) {
            // decide which pdf library to use based on config. 
            // #Also used on: modules/sale/controllers/BillController.php
            $pdf_company = Config::getConfig('pdf_company')->description;
            //var_dump($pdf_company);die(); // uncomment to see which option is currently enabled
            if($pdf_company == "westnet") return $this->WestnetPdf($company_id, $node_id, $qty);
            else if($pdf_company == "bigway") return $this->BigwayPdf($company_id, $node_id, $qty);
            //...

        }else{
            return $this->render('empty-ads');
        }
    }

    private function WestnetPdf($company_id, $node_id, $qty){
            // find node
            $node = Node::findOne(['node_id' => $node_id]);
            // find company
            $company = Company::findOne(['company_id'=> $company_id]);
            // generate payment code
            $generator = CodeGeneratorFactory::getInstance()->getGenerator('PagoFacilCodeGenerator');
            // generate new customer.code
            $init_value = Customer::getNewCode();
            // define an array that will store an associative array of $payment_code and $init_value
            $codes = [];

            
            for ($i = 0; $i < $qty; $i++) {
                /**
                 * El total del digitos del codigo de pago debe ser 14, por lo que la identificacion del cliente debe tener como maximo 8 digitos
                 */
                $complete = '';
                if ($company->code != '9999') {
                    $complete = str_pad($complete, (8 - strlen($init_value)), '0', STR_PAD_LEFT);
                }
                
                // generate payment code *goes below barcode*
                // pads with ceros the space between the company code and customer code. 
                $code = str_pad($company->code, 4, "0", STR_PAD_LEFT) . $complete .
                    str_pad($init_value, 5, "0", STR_PAD_LEFT) ;
                $payment_code= $generator->generate($code);
                $codes[] = ['payment_code'=> $payment_code, 'code' => $init_value];

                // define, instantiate and add data to a new 'Empty ADS' object. (Alta De Servicio)
                $emptyAds= new EmptyAds();
                $emptyAds->code = $init_value;
                $emptyAds->payment_code= $payment_code;
                $emptyAds->node_id= $node->node_id;
                $emptyAds->company_id= $company->company_id;
                $emptyAds->used= false;
                $emptyAds->save(false);

                // generate new customer code for every loop
                $init_value = Customer::getNewCode();
            }
        
            /*
             * At this point you can opt for a different PDF generation library
            */

            // change the yii2 layout
            $this->layout = '//pdf';

            $plans = $this->getPlans($company_id);

            $view = $this->render('pdf', [
                'codes'     => $codes,
                'node'      => $node,
                'company'   => $company,
                'plans'     => $plans
            ]);

            $response = Yii::$app->getResponse();
            $response->format = Response::FORMAT_RAW;
            $response->headers->set('Content-type: application/pdf');
            $response->setDownloadHeaders('bill.pdf', 'application/pdf', true);

            return PDFService::makePdf($view);
            
    }

    /**
     * prints the PDF for EmptyAds for BigWay. which has a different layout.
     */
    private function BigwayPdf($company_id, $node_id, $qty){
            // find node
            $node = Node::findOne(['node_id' => $node_id]);
            // find company
            $company = Company::findOne(['company_id'=> $company_id]);
            // generate payment code
            $generator = CodeGeneratorFactory::getInstance()->getGenerator('PagoFacilCodeGenerator');
            // generate new customer.code
            $init_value = Customer::getNewCode();
            // define an array that will store an associative array of $payment_code and $init_value
            $codes = [];

            
            for ($i = 0; $i < $qty; $i++) {
                /**
                 * El total del digitos del codigo de pago debe ser 14, por lo que la identificacion del cliente debe tener como maximo 8 digitos
                 */
                $complete = '';
                if ($company->code != '9999') {
                    $complete = str_pad($complete, (8 - strlen($init_value)), '0', STR_PAD_LEFT);
                }
                
                // generate payment code *goes below barcode*
                // pads with ceros the space between the company code and customer code. 
                $code = str_pad($company->code, 4, "0", STR_PAD_LEFT) . $complete . str_pad($init_value, 5, "0", STR_PAD_LEFT) ;
                $payment_code= $generator->generate($code);
                $codes[] = ['payment_code'=> $payment_code, 'code' => $init_value];

                // define, instantiate and add data to a new 'Empty ADS' object. (Alta De Servicio)
                $emptyAds= new EmptyAds();
                $emptyAds->code = $init_value;
                $emptyAds->payment_code= $payment_code;
                $emptyAds->node_id= $node->node_id;
                $emptyAds->company_id= $company->company_id;
                $emptyAds->used= false;
                $emptyAds->save(false);

                // generate new customer code for every loop
                $init_value = Customer::getNewCode();
            }
        
            /*
             *
             * At this point you can opt for a different PDF generation library
             * 
            */

            // change the yii2 layout
            $this->layout = '//pdf';

            $plans = $this->getPlans($company_id);

            
            $view = $this->render('pdf', [
                'codes'     => $codes,
                'node'      => $node,
                'company'   => $company,
                'plans'     => $plans
            ]);

            $response = Yii::$app->getResponse();
            $response->format = Response::FORMAT_RAW;
            $response->headers->set('Content-type: application/pdf');
            $response->setDownloadHeaders('bill.pdf', 'application/pdf', true);

            return PDFService::makePdf($view);

            /**
             * for this ADS we need:
             * todays DATE
             * customer code (ADS)
             * Acomodatos? (onu, roseta, patchcore)
             * tipo de conexion (hogar/empresa , fibra optica)
             * velocidad (planes: 25,50,100,300)
             * info harcoded: aceptacion del servicio, contactos
             */

            // todays DATE

            // NEW customer code (ADS) based on $qty

            // obtener acomodatos de instalacion

            // 
            
            
            // bigway copy from billcontroller

           

            $cupon_bill_types = explode(',', \app\modules\config\models\Config::getValue('cupon_bill_types'));
            $is_cupon = (array_search($model->bill_type_id, $cupon_bill_types) !==false);
            $payment = new Payment();
            $payment->customer_id = $model->customer_id;
            $debt = $payment->accountTotal();
            $isConsumidorFinal = false;
            $profile = $model->customer->getCustomerProfiles()->where(['name'=>'Consumidor Final'])->one();
            $company = (isset($company) ? $company : $model->customer->parentCompany );
            $companyData = $model->company;

            //echo'<pre>'; var_dump( $companyData->name  ); die;

            $cuit = str_replace('-', '', $model->company->tax_identification);
            $code = $cuit . sprintf("%02d", $model->billType->code) . sprintf("%04d", $model->getPointOfSale()->number) . $model->ein . (new \DateTime($model->ein_expiration))->format("Ymd");

            $barcode = new BarcodeGeneratorPNG();

            $jsonCode = [
                        "ver" => 1,
                        "fecha" => $model->date,
                        "cuit" => str_replace("-","",$companyData->tax_identification),
                        "ptoVta" => $model->getPointOfSale()->number,
                        "tipoCmp" => $model->billType->code,
                        "nroCmp" => $model->number,
                        "importe" => $model->total,
                        "moneda" => "PES",
                        "ctz" => 1,
                        "tipoDocRec" => $model->customer->documentType->code,
                        "nroDocRec" => str_replace("-","",$model->customer->document_number),
                        "tipoCodAut" => "E",
                        "codAut" => $model->ein
                        ];
            $qrCode = (new QrCode("https://www.afip.gob.ar/fe/qr/?p=".base64_encode(json_encode($jsonCode))))
            ->setSize(500)
            ->setMargin(5);
            
            $formatter = Yii::$app->formatter;

            $content = $this->renderPartial('bigway-pdf.php',[
                'model' => $model,
                'dataProvider' => $dataProvider,
                'formatter' => $formatter,
                'cupon_bill_types' => $cupon_bill_types,
                'is_cupon' => $is_cupon,
                'payment' => $payment,
                'debt' => $debt,
                'isConsumidorFinal' => $isConsumidorFinal,
                'profile' => $profile,
                'company' => $company,
                'companyData' => $companyData,
                'barcode' => $barcode,
                'code' => $code,
                'qrCode' => $qrCode

            ]);

                
            $pdf = new Pdf([
                
                'mode' => Pdf::MODE_UTF8, 
                
                'format' => Pdf::FORMAT_LEGAL, 
            
                'orientation' => Pdf::ORIENT_PORTRAIT, 
                
                'destination' => Pdf::DEST_BROWSER, 
            
                'content' => $content,  
                'filename' => "documento.pdf",
                'cssFile' => '@app/modules/sale/web/css/sale-bill-pdf.css',
                
                'options' => ['title' => ""],
                
                'methods' => [ 
                    'SetTitle' => '',
                    'SetFooter'=>['Página {PAGENO} de {nb}'],
                ],
                'marginTop' => 5,
            ]);

                
            return $pdf->render();
    }
    /**
     *  Imprime un Ads en blanco ya generado
     * @param type $emptyAds_id
     * @return type
     */
    public function actionPrintOneEmptyAds($empty_ads_id){
        $this->layout = '//pdf';

        $emptyAds= EmptyAds::findOne(['empty_ads_id' => $empty_ads_id]);
        $node = Node::findOne(['node_id'=> $emptyAds->node_id]);

        $plans = $this->getPlans($emptyAds->company_id);

        $view = $this->render('pdf', [
            'codes' => [['payment_code' => $emptyAds->payment_code, 'code'=> $emptyAds->code]],
            'node'  =>  $node,
            'plans' => $plans,
            'company' => $emptyAds->company
        ]);

        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_RAW;
        $response->headers->set('Content-type: application/pdf');
        $response->setDownloadHeaders('bill.pdf', 'application/pdf', true);

        return PDFService::makePdf($view);
    }
    /**
     * Finds the IpRange model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return IpRange the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Contract::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionBarcode($code, $mode= '0') {
        $barcode = new Barcode();
        $barcode->setGenbarcodeLocation(Yii::$app->params['genbarcode_location']);
        $barcode->setMode(Barcode::MODE_PNG);
        if($mode === '0' ){
            $barcode->setEncoding(Barcode::ENCODING_ANY);
        }else{
            $barcode->setEncoding(Barcode::ENCODING_128);
        }
        $response = Yii::$app->getResponse();
        $response->headers->set('Content-Type', 'image/png');
        $response->format = Response::FORMAT_RAW;

        return $barcode->outputImage($code);
    }

    public function actionPrintAdsByBatch()
    {
        $ids_contracts = Yii::$app->request->post('contracts');
        $adses= [];
        $plans = [];

        foreach ($ids_contracts as $id) {
            $contract= $this->findModel($id);
            $node = Node::findOne(['subnet' => $contract->tentative_node]);
            CompanyByNode::setCompanyToCustomer($node, $contract->customer);

            $code = $contract->customer->code;
            $payment_code= $contract->customer->payment_code;
            if(array_key_exists($contract->customer->parent_company_id."-".$contract->customer->customerCategory->name, $plans )===false) {
                $plan = $this->getPlans($contract->customer->parent_company_id, $contract->customer->customerCategory->name);
                $plans[$contract->customer->parent_company_id."-".$contract->customer->customerCategory->name] = $plan;
            }
            $ads=[
                'model'         => $contract,
                'node'          => $node,
                'code'          => $code,
                'payment_code'  => $payment_code,
                'company'       => $contract->customer->parentCompany,
                'plans'         => $plans[$contract->customer->parent_company_id."-".$contract->customer->customerCategory->name]
            ];
            $adses[]= $ads;
        }
        
        $this->layout = '//pdf';
        
        $view = $this->render('pdfByBatch', ['adses' => $adses]);

        $response = Yii::$app->getResponse();
        $response->format = \yii\web\Response::FORMAT_RAW;
        $response->headers->set('Content-type: application/pdf');
        $response->setDownloadHeaders('ads.pdf', 'application/pdf', true);

        return \app\components\helpers\PDFService::makePdf($view);
        
    }

    /**
     * Funcion que retorna los posibles nodos dependiendo de la empresa padre enviada.
     */
    public function actionChildNodes()
    {
        $out = [];
        $params = Yii::$app->request->post('depdrop_all_params');
        $company_id = ($params['parent_company_id'] ? $params['parent_company_id'] : null);
        if ($company_id) {
            $query = Node::find();
            $query
                ->select(['node.node_id as id', 'node.name as name'])
                ->leftJoin('node_has_company nhc', 'node.node_id = nhc.node_id')
                ->leftJoin('company c', 'nhc.company_id = c.company_id')
                ->where(['c.parent_id'=>$company_id])
            ;
            $out = $query->distinct()->asArray()->all();
            echo Json::encode(['output'=>$out, 'selected'=>'']);
        } else {
            echo Json::encode(['output'=>'', 'selected'=>'']);
        }
    }

    public function actionEtiquetas()
    {
        $this->layout = '//empty';

        $view = $this->render('etiquetas');

        $response = Yii::$app->getResponse();
        $response->format = \yii\web\Response::FORMAT_RAW;
        $response->headers->set('Content-type: application/pdf');
        $response->setDownloadHeaders('etiquetas.pdf', 'application/pdf', true);

        return \app\components\helpers\PDFService::makePdf($view);

    }

    /**
     * @param $company_id
     * @return array
     */
    private function getPlans($company_id, $category = 'Familia')
    {
        $plans = [];
        $leftProductPrice = ProductPrice::find()
            ->select(['product_price_id', 'product_id', 'max(timestamp)'])
            ->groupBy(['product_id']);

        $subQueryplans = Product::find()
            ->where(['product.type'=>'plan', 'product.status' => 'enabled' ])
            ->andWhere(['or',
                ['company_id'=>$company_id],
                ['company_id'=>null]
            ])
            ->leftJoin(['ppm' => $leftProductPrice], 'ppm.product_id = product.product_id')
            ->leftJoin('product_price pp', 'ppm.product_price_id = pp.product_price_id')
            ->distinct()
            ->orderBy(['pp.net_price'=>SORT_DESC]);

        if ($category === 'Familia') {
            $queryplans = Product::find()
                ->from(['sub' => $subQueryplans])
                ->joinWith('categories')
                ->andWhere(['category.system' => 'planes-de-internet-residencial']);
            $plans = $queryplans->all();

        }elseif($category === 'Empresa'){
            $queryplans = Product::find()
                ->from(['sub' => $subQueryplans])
                ->joinWith('categories')
                ->andWhere(['category.system' => 'planes-de-internet-empresa']);
            $plans = $queryplans->all();
        }else{
            $plans= $subQueryplans->all();
        }

        return $plans;
    }
}