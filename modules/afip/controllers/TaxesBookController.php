<?php

namespace app\modules\afip\controllers;

use app\components\helpers\ExcelExporter;
use app\modules\afip\exports\compraVenta\CompraVenta;
use app\modules\afip\exports\compraVenta\CompraVentaAlicuotas;
use app\modules\afip\exports\compraVenta\CompraVentaComprobantes;
use app\modules\afip\models\search\IibbSearch;
use app\modules\afip\models\search\TaxesBookSearch;
use app\modules\afip\models\TaxesBookItem;
use app\modules\sale\models\search\BillSearch;
use app\modules\sale\models\TaxRate;
use PHPExcel_Style_NumberFormat;
use Yii;
use app\modules\afip\models\TaxesBook;
use yii\data\ActiveDataProvider;
use app\components\web\Controller;
use yii\data\Pagination;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\helpers\EmptyLogger;

/**
 * TaxesBookController implements the CRUD actions for TaxesBook model.
 */
class TaxesBookController extends \app\components\web\Controller
{

    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }


    /**
     * Lists all TaxesBook models.
     * @return mixed
     */
    public function actionBuy()
    {
        $searchModel = new TaxesBookSearch();
        $searchModel->type = 'buy';

        $query = $searchModel->search(Yii::$app->request->queryParams);
        $query->orderBy(['period' => SORT_DESC, 'company_id' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'type' => 'buy',
            'searchModel' => $searchModel
        ]);
    }

    /**
     * Lists all TaxesBook models.
     * @return mixed
     */
    public function actionSale()
    {
        $searchModel = new TaxesBookSearch();
        $searchModel->type = 'sale';

        $query = $searchModel->search(Yii::$app->request->queryParams);
        $query->orderBy(['period' => SORT_DESC, 'company_id' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'type' => 'sale',
            'searchModel' => $searchModel
        ]);
    }

    /**
     * Displays a single TaxesBook model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TaxesBook model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($type)
    {
        $model = new TaxesBook();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->taxes_book_id]);
        } else {
            $model->type = $type;
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TaxesBook model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->taxes_book_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing TaxesBook model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $type = $model->type;
        $model->delete();

        if ($type=='buy') {
            return $this->redirect(['buy']);
        } else {
            return $this->redirect(['sale']);
        }
    }

    /**
     * Finds the TaxesBook model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TaxesBook the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TaxesBook::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     * Retorna una vista con los comprobantes comprendidos en el libro de ventas.
     * Si el estado del libro es draft muestra las que acaba de añadir.
     * Si el estado es otro (closed x ejemplo) muestra los registros de los items del
     * libro de iva
     */
    public function actionAddSaleBills($id)
    {
        $model = $this->findModel($id);

        $totals = [
            'amount' => 0,
            'taxes' => 0,
            'total' => 0,
        ];

        if (!$model->getTaxesBookItems()->exists()) {
            $searchModel = new TaxesBookSearch([
                'fromDate' => $model->period,
                'company_id' => $model->company_id,
                'bill_types' => ArrayHelper::getColumn( $model->company->billTypes, 'bill_type_id'),
            ]);
            $dataProvider = new ActiveDataProvider([
                'query' => $searchModel->findBillSale(),
            ]);

            $query = $searchModel->findBillSale()->addSelect(['(bill.amount*bt.multiplier) as amount', new Expression('bill.total*bt.multiplier as total'), '(bill.taxes*bt.multiplier) as taxes'])
                ->leftJoin('bill_type bt', 'bill.bill_type_id = bt.bill_type_id');
        } else {
            $dataProvider = new ActiveDataProvider([
                'query' => $model->getTaxesBookItems()
            ]);

            $query = $model->getTaxesBookItems()
                ->addSelect(['(b.amount*bt.multiplier) as amount', new Expression('b.total*bt.multiplier as total'), '(b.taxes*bt.multiplier) as taxes'])
                ->leftJoin('bill b', 'taxes_book_item.bill_id = b.bill_id')
                ->leftJoin('bill_type bt', 'b.bill_type_id = bt.bill_type_id');
        }

        $mainquery = new Query();
        $mainquery->select(['sum(amount) as amount', 'sum(total) as total', 'sum(taxes) as taxes'])
            ->from(['p'=>$query]);
        $totals = $mainquery->one();

        return $this->render('add-sale-bills', [
            'model'         => $model,
            'dataProvider'  => $dataProvider,
            'totals' => $totals,
        ]);
    }


    public function actionAddBuyBills($id)
    {
        $model = $this->findModel($id);
        $this->layout = '//fluid';
        return $this->render('add-buy-bills', [
            'model' => $model,
        ]);
    }

    public function buyBills($id)
    {
        $model = $this->findModel($id);

        $searchModel  = new TaxesBookSearch();
        $searchModel->taxes_book_id = $id;
        $searchModel->company_id = $model->company_id;
        $searchModel->toDate = (new \DateTime($model->period))->format('Y-m-t');
        $searchModel->provider_id = Yii::$app->request->post('provider_id', Yii::$app->request->get('provider_id', null));
        $searchModel->bill_types =  Yii::$app->request->post('bill_types', Yii::$app->request->get('bill_types', null));

        if(!$searchModel->bill_types) {
            foreach($model->company->taxCondition->getBillTypesBuy()->select(['bill_type_id'])->asArray()->all() as $bt) {
                $searchModel->bill_types[] = $bt['bill_type_id'];
            }
        } else {
            if(strpos($searchModel->bill_types[0], ',')!==false) {
                $searchModel->bill_types = explode(',', $searchModel->bill_types[0]);
            }
        }

        $dataProvider = $searchModel->findBuyBills(Yii::$app->request->getQueryParams());

        $paginator = $dataProvider->getPagination();
        $paginator->params = [
            'id'=>$searchModel->taxes_book_id,
            'per-page' => 10,
        ];

        if($searchModel->provider_id){
            $paginator->params['provider_id'] = $searchModel->provider_id;
        }

        $paginator->page = Yii::$app->request->get('page-bills', Yii::$app->request->post('page',1))-1;
        $dataProvider->setPagination($paginator);

        $this->layout = '//embed';
        return $this->renderAjax('_buy-bills', [
            'model' => $model,
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'bills' => true
        ]);
    }

    public function buyEmployeeBills($id)
    {
        $model = $this->findModel($id);

        $searchModel  = new TaxesBookSearch();
        $searchModel->taxes_book_id = $id;
        $searchModel->company_id = $model->company_id;
        $searchModel->toDate = (new \DateTime($model->period))->format('Y-m-t');
        $searchModel->employee_id = Yii::$app->request->post('employee_id', Yii::$app->request->get('employee_id', null));
        $searchModel->bill_types =  Yii::$app->request->post('bill_types', Yii::$app->request->get('bill_types', null));

        if(!$searchModel->bill_types) {
            foreach($model->company->taxCondition->getBillTypesBuy()->select(['bill_type_id'])->asArray()->all() as $bt) {
                $searchModel->bill_types[] = $bt['bill_type_id'];
            }
        } else {
            if(strpos($searchModel->bill_types[0], ',')!==false) {
                $searchModel->bill_types = explode(',', $searchModel->bill_types[0]);
            }
        }

        $dataProvider = $searchModel->findBuyEmployeeBills(Yii::$app->request->getQueryParams());

        $paginator = $dataProvider->getPagination();
        $paginator->params = [
            'id'=>$searchModel->taxes_book_id,
            'per-page' => 10,
        ];

        if($searchModel->employee_id){
            $paginator->params['employee_id'] = $searchModel->employee_id;
        }

        $paginator->page = Yii::$app->request->get('page-bills', Yii::$app->request->post('page',1))-1;
        $dataProvider->setPagination($paginator);

        $this->layout = '//embed';
        return $this->renderAjax('_buy-employee-bills', [
            'model' => $model,
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'bills' => true
        ]);
    }

    public function buyBillsAdded($id)
    {
        $model = $this->findModel($id);

        $searchModel  = new TaxesBookSearch();
        $searchModel->taxes_book_id = $id;
        $searchModel->for_print = true;
        $searchModel->company_id = $model->company_id;
        $searchModel->bill_types =  ArrayHelper::getColumn( $model->company->taxCondition->billTypesBuy, 'bill_type_id');
        $dataProvider = $searchModel->findBuyBills(Yii::$app->request->getQueryParams());

        $dataProvider2 = $searchModel->findBuyEmployeeBills(Yii::$app->request->getQueryParams());
        $dataProvider->query->union($dataProvider2->query);

        $paginator = $dataProvider->getPagination();
        $paginator->params = [
            'id'=>$searchModel->taxes_book_id,
            'per-page' => 10,
        ];

        if($searchModel->provider_id){
            $paginator->params['provider_id'] = $searchModel->provider_id;
        }
        $paginator->page = Yii::$app->request->get('page-added', Yii::$app->request->post('page',1))-1;

        $this->layout = '//embed';
        return $this->renderAjax('_buy-bills', [
            'model' => $model,
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'bills' => false
        ]);
    }

    public function buyBillsTotals($id)
    {
        $model = $this->findModel($id);
        $searchModel  = new TaxesBookSearch();
        $searchModel->taxes_book_id = $id;
        $searchModel->company_id = $model->company_id;
        $totals = $searchModel->findTotals(Yii::$app->request->getQueryParams());

        $this->layout = '//embed';
        return $this->renderAjax('_buy-totals', [
            'totals' => $totals
        ]);
    }

    public function actionAddBill($id)
    {
        $model = $this->findModel($id);
        Yii::$app->response->format = 'json';

        $provider_bill_id = Yii::$app->request->post('provider_bill_id', null);
        $employee_bill_id = Yii::$app->request->post('employee_bill_id', null);

        if ($provider_bill_id !== null) {
            foreach ($provider_bill_id as $bill) {
                if (empty(TaxesBookItem::find()->where(['taxes_book_id' => $id, 'provider_bill_id' => $bill])->one())) {
                    $item = new TaxesBookItem();
                    $item->taxes_book_id = $id;
                    $item->page = 0;
                    $item->provider_bill_id = $bill;
                    $item->taxes_book_id = $id;
                    $item->save();
                }
            }

            $buy_bills_view = $this->buyBills($id);
            $buy_employee_bills_view = $this->buyEmployeeBills($id);
            $buy_bills_added_view = $this->buyBillsAdded($id);
            $total_view = $this->buyBillsTotals($id);

            return [
                'status' => 'success',
                'buy_bills' => $buy_bills_view,
                'buy__employee_bills' => $buy_employee_bills_view,
                'buy_bills_added' => $buy_bills_added_view,
                'total' => $total_view
            ];
        }

        if ($employee_bill_id !== null) {
            foreach ($employee_bill_id as $bill) {
                if (empty(TaxesBookItem::find()->where(['taxes_book_id' => $id, 'employee_bill_id' => $bill])->one())) {
                    $item = new TaxesBookItem();
                    $item->taxes_book_id = $id;
                    $item->page = 0;
                    $item->employee_bill_id = $bill;
                    $item->taxes_book_id = $id;
                    $item->save();
                }
            }

            $buy_bills_view = $this->buyBills($id);
            $buy_employee_bills_view = $this->buyEmployeeBills($id);
            $buy_bills_added_view = $this->buyBillsAdded($id);
            $total_view = $this->buyBillsTotals($id);

            return [
                'status' => 'success',
                'buy_bills' => $buy_bills_view,
                'buy__employee_bills' => $buy_employee_bills_view,
                'buy_bills_added' => $buy_bills_added_view,
                'total' => $total_view
            ];
        }


        return [
            'status' => 'fail'
        ];

    }

    public function actionRemoveBill($id)
    {
        $model = $this->findModel($id);
        Yii::$app->response->format = 'json';

        $provider_bill_id = Yii::$app->request->post('provider_bill_id', null);
        if ($provider_bill_id !== null) {
            foreach ($provider_bill_id as $bill) {
                TaxesBookItem::deleteAll(['taxes_book_id' => $id, 'provider_bill_id' => $bill]);
            }

            $buy_bills_view = $this->buyBills($id);
            $buy_bills_added_view = $this->buyBillsAdded($id);
            $total_view = $this->buyBillsTotals($id);

            return [
                'status' => 'success',
                'buy_bills' => $buy_bills_view,
                'buy_bills_added' => $buy_bills_added_view,
                'total' => $total_view
            ];
        } else {
            return [
                'status' => 'fail'
            ];
        }
    }

    public function actionClose($id)
    {
        set_time_limit(0);
        Yii::$app->response->format = 'json';

        $model = $this->findModel($id);
        $return = [];

        if ($model->can(TaxesBook::STATE_CLOSED) && $model->close()) {
            $return['status'] = 'success';
        } else {
            $return['status'] = 'fail';
            $return['message'] = Yii::t('afip', 'This book could not be closed.');
        }

        return $return;
    }

    /**
     * Guarda los items de taxes book pero deja el libro en estado borrador,
     * de modo que se puedan exportar los archivos pdf, excel y cvs sin tener aun el libro cerrado
     */
    public function actionSave($id)
    {
        set_time_limit(0);
        Yii::$app->response->format = 'json';

        $model = $this->findModel($id);
        $return = [];

        if ($model->saveItems()) {
            $return['status'] = 'success';
        } else {
            $return['status'] = 'fail';
            $return['message'] = Yii::t('afip', 'This book could not be closed.');
        }

        return $return;
    }

    public function actionPrint($id)
    {
        set_time_limit(0);
        Yii::setLogger(new EmptyLogger());

        $model = $this->findModel($id);
        Yii::$app->response->format = 'pdf';
        $this->layout = '//pdf';
        Yii::$app->htmlToPdf->options['orientation'] = 'landscape';
        $searchModel  = new TaxesBookSearch();
        $searchModel->for_print = true;
        $searchModel->taxes_book_id = $id;
        $searchModel->company_id = $model->company_id;

        if ($model->type == 'buy') {
            $dataProvider = $searchModel->findBuyTxt(Yii::$app->request->getQueryParams());
        } else {
            $dataProvider = $searchModel->findSale(Yii::$app->request->getQueryParams());
        }
        $dataProvider->setPagination(false);

        return $this->renderPartial('_print', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'excel' => false
        ]);
    }

    public function actionExportExcel($id)
    {
        set_time_limit(0);
        //Yii::setLogger(new EmptyLogger());

        $model = $this->findModel($id);
        Yii::$app->htmlToPdf->options['orientation'] = 'landscape';
        $searchModel  = new TaxesBookSearch();
        $searchModel->for_print = true;
        $searchModel->taxes_book_id = $id;
        $searchModel->company_id = $model->company_id;

        if ($model->type == 'buy') {
            $dataProvider = $searchModel->findBuyTxt(Yii::$app->request->getQueryParams());
        } else {
            $dataProvider = $searchModel->findSale(Yii::$app->request->getQueryParams());
        }
        $dataProvider->setPagination(false);

        return $this->renderPartial('_print', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'excel' => true
        ]);
    }

    /**
     * @return string
     */
    public function actionIibbProducts()
    {
        set_time_limit(0);
        Yii::setLogger(new EmptyLogger());

        $isSearch = Yii::$app->request->post('search', true);
        if($isSearch) {
            $iibb = new IibbSearch();
            $iibb->load(Yii::$app->request->bodyParams);
            $dataProvider = new ActiveDataProvider([
                'query' => $iibb->findIIBB(),
            ]);
            $dataProvider->setPagination(false);

            return $this->render('iibb', [
                'dataProvider' => $dataProvider,
                'searchModel' => $iibb
            ]);
        } else {
            return $this->actionExportExcelIIBB();
        }
    }

    public function actionExportExcelIIBB()
    {
        set_time_limit(0);
        Yii::setLogger(new EmptyLogger());

        $iibb = new IibbSearch();
        $iibb->load(Yii::$app->request->bodyParams);

        $data = $iibb->findIIBB()->all();

        $excel = ExcelExporter::getInstance();
        $excel->create('caja-chica', [
            'A' => ['product',  Yii::t('app', 'Product'), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
            'B' => ['qty', Yii::t('app', 'Qty'), PHPExcel_Style_NumberFormat::FORMAT_NUMBER],
            'C' => ['total', Yii::t('app', 'total'), PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00],
        ])->createHeader();

        $total = 0;
        foreach ($data as $key => $item){
            $excel->writeRow([
                'product'=> $item['product'],
                'qty'=> $item['qty'],
                'total'=> $item['total'],
            ]);
            $total+=$item['total'];
        }
        $excel->setRow($excel->getRow()+2);
        $excel->writeRow([
            'product'=> '',
            'qty'=> '',
            'total'=> $total
        ]);

        $excel->download('productos-para-iibb.xls');
    }

    public function actionExportTxt($id, $type)
    {
        set_time_limit(0);
        Yii::setLogger(new EmptyLogger());

        $model = $this->findModel($id);

        $searchModel  = new TaxesBookSearch();
        $searchModel->taxes_book_id = $id;
        $searchModel->company_id = $model->company_id;

        $fileName = '';
        if ($model->type == 'buy') {
             $dataProvider = $searchModel->findBuyTxt(Yii::$app->request->getQueryParams());
        } else {
            $dataProvider = $searchModel->findSaleTxt(Yii::$app->request->getQueryParams());
        }

        if($type=='alicuotas') {
            $fileName = 'RegInfo_'.($model->type=='buy'?'Compras': 'Ventas').'_Alicuotas.txt';
            $cva = new CompraVentaAlicuotas($dataProvider->getModels(), ($model->type != 'buy'));
        } else {
            $fileName = 'RegInfo_'.($model->type=='buy'?'Compras': 'Ventas').'_Comprobantes.txt';
            $cva = new CompraVentaComprobantes($dataProvider->getModels(), ($model->type != 'buy'));
        }
        $cva->parse();

        header('Content-Type: text/plain');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        try {
            $cva->writeFile('php://output');
        } catch( \Exception $ex) {
            error_log($ex->getMessage());
        }
    }

    public function actionInitialDataBuy($id)
    {
        Yii::$app->response->format = 'json';
        $buy_bills_view = $this->buyBills($id);
        $buy_employee_bills_view = $this->buyEmployeeBills($id);
        $buy_bills_added_view = $this->buyBillsAdded($id);
        $total_view = $this->buyBillsTotals($id);

        return [
            'status' => 'success',
            'buy_bills' => $buy_bills_view,
            'buy_employee_bills' => $buy_employee_bills_view,
            'buy_bills_added' => $buy_bills_added_view,
            'total' => $total_view
        ];
    }

}
