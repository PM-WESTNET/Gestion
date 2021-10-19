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
            $node = Node::findOne(['node_id' => $node_id]); // doest contemplate finding a null object here
            $company = Company::findOne(['company_id'=> $company_id]);

            $generator = CodeGeneratorFactory::getInstance()->getGenerator('PagoFacilCodeGenerator');

            $init_value = Customer::getNewCode();
            $codes = [];

            for ($i = 0; $i < $qty; $i++) {
                /**
                 * El total del digitos del codigo de pago debe ser 14, por lo que la identificacion del cliente debe tener como maximo 8 digitos
                 */
                $complete = '';
                if ($company->code != '9999') {
                    $complete = str_pad($complete, (8 - strlen($init_value)), '0', STR_PAD_LEFT);
                }

                $code = str_pad($company->code, 4, "0", STR_PAD_LEFT) . $complete .
                    str_pad($init_value, 5, "0", STR_PAD_LEFT) ;

                $payment_code= $generator->generate($code);
                $codes[] = ['payment_code'=> $payment_code, 'code' => $init_value];
                $emptyAds= new EmptyAds();
                $emptyAds->code = $init_value;
                $emptyAds->payment_code= $payment_code;
                $emptyAds->node_id= $node->node_id;
                $emptyAds->company_id= $company->company_id;
                $emptyAds->used= false;
                $emptyAds->save(false);
                $init_value = Customer::getNewCode();
            }

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
            
        }else{
            return $this->render('empty-ads');
        }
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