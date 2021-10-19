<?php
namespace app\components\pdf;

use Yii;
use yii\base\Component;
use app\modules\config\models\Config;
use Da\QrCode\QrCode;
use app\modules\checkout\models\Payment;
use Picqer\Barcode\BarcodeGeneratorPNG;
use kartik\mpdf\Pdf;


/**
 * Description of PDFService
 *
 * @author mmoyano
 */
class PdfUtils extends Component{
    // selects the PDF generation library based on the APP configuration items.
    public function actionPdf($id){
        // gets conf item
        $pdf_company = Config::getConfig('pdf_company')->description;
        var_dump($pdf_company);die();
        if($pdf_company == "westnet")
            return $this->WestnetPdf($id);
        
        else if($pdf_company == "bigway")
            return $this->BigwayPdf($id);
        
    }


    /**
     * Prints the pdf of a single Bill Westnet.
     * @param integer $id
     * @return mixed
     */
    public function WestnetPdf($id)
    {

        $response = Yii::$app->getResponse();
        $response->format = \yii\web\Response::FORMAT_RAW;
        $response->headers->set('Content-type: application/pdf');
        $response->setDownloadHeaders('bill.pdf', 'application/pdf', true);

        $model = $this->findModel($id);
        $companyData = $model->company;

        $this->layout = '//pdf';

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $model->getBillDetails(),
            'pagination' => false
        ]);

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

        $view = $this->render('pdf', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'qrCode' => $qrCode

        ]);

        $pdf = ' ';

        try{
            $pdf = \app\components\helpers\PDFService::makePdf($view);
        } catch (\Exception $ex){
            \Yii::trace($ex);
        }

        return $pdf;
    }

    /**
     * Prints the pdf of a single Bill Bigway.
     * @param integer $id
     * @return mixed
     */
    public function BigwayPdf($id)
    {      
        $model = $this->findModel($id);
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $model->getBillDetails(),
            'pagination' => false
        ]);

        $formatter = Yii::$app->formatter;
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


}
?>