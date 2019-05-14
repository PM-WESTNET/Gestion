<?php

namespace app\modules\checkout\models;

use app\components\db\ActiveRecord;
use app\modules\accounting\models\MoneyBoxAccount;
use app\modules\pagomiscuentas\components\Cobranza\PagoFacilReader;
use app\modules\sale\models\Customer;
use Yii;
use yii\db\Expression;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "pago_facil_transmition_file".
 *
 * @property integer $pago_facil_transmition_file_id
 * @property string $header_file
 * @property string $upload_date
 * @property integer $money_box_account_id
 * @property double $total  
 * @property File $file
 * @property integer $money_box
 * @property string $file_name
 * @property string $status
 */
class PagoFacilTransmitionFile extends ActiveRecord {


    const STATUS_DRAFT = 'draft';
    const STATUS_CLOSED = 'closed';

    public $file;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'pago_facil_transmition_file';
    }

    /**
     * @inheritdoc
     */
    /*
      public function behaviors()
      {
      return [
      'timestamp' => [
      'class' => 'yii\behaviors\TimestampBehavior',
      'attributes' => [
      yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['timestamp'],
      ],
      ],
      'date' => [
      'class' => 'yii\behaviors\TimestampBehavior',
      'attributes' => [
      yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date'],
      ],
      'value' => function(){return date('Y-m-d');},
      ],
      'time' => [
      'class' => 'yii\behaviors\TimestampBehavior',
      'attributes' => [
      yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['time'],
      ],
      'value' => function(){return date('h:i');},
      ],
      ];
      }
     */

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['money_box_account_id', 'money_box_id'], 'required'],
            [['upload_date', 'header_file'], 'safe'],
            [['upload_date'], 'date'],
            [['header_file'], 'string', 'max' => 256],
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => '900'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'pago_facil_transmition_file_id' => 'Idpago Facil Transmition File',
            'header_file' => 'Header File',
            'upload_date' => Yii::t('app', 'Upload Date'),
            'file' => Yii::t('app', 'File'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            $this->formatDatesBeforeSave();
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function afterFind() {
        $this->formatDatesAfterFind();
        parent::afterFind();
    }

    /**
     * @brief Format dates using formatter local configuration
     */
    private function formatDatesAfterFind() {
        $this->upload_date = Yii::$app->formatter->asDate($this->upload_date);
    }

    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave() {
        $this->upload_date = Yii::$app->formatter->asDate($this->upload_date, 'yyyy-MM-dd');
    }

    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable() {
        return true;
    }

    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: None.
     */
    protected function unlinkWeakRelations() {
        
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete() {
        if (parent::beforeDelete()) {
            if ($this->getDeletable()) {
                $this->unlinkWeakRelations();
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Verifica si el archivo a importar ha sido importado antes 
     * @return boolean
     */
    public function isRepeat() {
        $file = PagoFacilTransmitionFile::findOne(['file_name' => $this->file_name]);
        
        if (empty($file)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Recorre el archivo de pago facil cargado y genera los payments correspondientes
     * @return boolean
     */
    public function import() {
        //Creo la instancia del archivo cargado
        $this->file = UploadedFile::getInstance($this, 'file');
        
        $directory = 'uploads/'.date('Y').'/'.date('m').'/';
        
        //Directorio donde subir archivos
        FileHelper::createDirectory($directory, 0775, true);
        $path = $directory . $this->file->baseName . uniqid() . '.' . $this->file->extension;
        
        $this->file->saveAs($path, false);
        
        $this->file_name = $path;
        $this->status= "draft";
        
        if (empty($this->money_box_account_id) && empty($this->money_box)) {
            Yii::$app->session->setFlash('error', 'Seleccione el banco y/o la cuenta destinatarios');
            return false;
        }
        
//        $data = fopen($path, 'r');
        $transaction = Yii::$app->db->beginTransaction(); // Inicia transaccion con la base de datos
        if (!$this->isRepeat()) {

            try {
                $array_data = PagoFacilReader::parse($this);

//                foreach ($array_data as $data) {
//
//                }

                            $customer = Customer::find()
                                ->orWhere(['customer.code' => $customer_id])->one();


                            if (!empty($customer)) {
                                // Creo el pago y seteo los atributos correspondientes
                                $payment = new Payment();
                                $payment->company_id = $customer->company_id;
                                $payment->customer_id = $customer->customer_id;
                                $payment->concept = "PAGO FACIL";
                                $payment->date = $date;
                                $payment->amount = $amount;
                                $payment->partner_distribution_model_id = $customer->company->partner_distribution_model_id;


                                if ($payment->save()) {

                                    $paymentDetail = new PaymentItem();


                                    $paymentMethod = PaymentMethod::find()
                                        ->where("status='enabled' AND lower(name) = '".strtolower($payment_method)."'")
                                        ->one();
                                    $moneyBoxAccount = MoneyBoxAccount::findOne(['money_box_account_id' => $this->money_box_account_id]);
                                    if (!empty($paymentMethod)) {
                                        $paymentDetail->payment_id = $payment->payment_id;
                                        $paymentDetail->amount = $amount;
                                        $paymentDetail->description = "PAGO FACIL";
                                        $paymentDetail->payment_method_id = $paymentMethod->payment_method_id;
                                        if ($moneyBoxAccount) {
                                            $paymentDetail->money_box_account_id = $moneyBoxAccount->money_box_account_id;
                                        }
                                        $paymentDetail->save(false);
                                    } else {
                                        $transaction->rollBack();
                                        Yii::$app->session->setFlash('error_payment', 'Error al guardar un pago. No hay metodo de pago. Reintente');
                                        return FALSE;
                                    }



                                    $pagoFacilPayment = new PagoFacilPayment();
                                    $pagoFacilPayment->pago_facil_transmition_file_pago_facil_transmition_file_id = $this->pago_facil_transmition_file_id;
                                    $pagoFacilPayment->payment_payment_id = $payment->payment_id;
                                    $pagoFacilPayment->save();
                                } else {
                                    $transaction->rollBack();
                                    Yii::$app->session->setFlash('error_payment', 'Error al guardar un pago. Reintente');
                                    return false;
                                }
                            }
//                }

                if (!$this->updateAttributes(['total'])) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('unsave_file', 'El archivo seleccionado no se puede importar. Reintente');
                    return false;
                }
                $transaction->commit(); // Finaliza transaccion
                fclose($data);
                unlink($path);

                return TRUE;
            } catch (\Exception $ex) {
                $transaction->commit();
                Yii::$app->session->setFlash('unsave_file', $ex->getMessage());
            }


        } else {
            $transaction->rollBack();
            unlink($this->file->baseName);
            Yii::$app->session->setFlash('error', 'El archivo seleccionado ya ha sido importado antes. Seleccione otro y reintente.');
            return FALSE;
        }
    }
    
    /**
     * 
     * @return ActiveQuery
     */
    public function getPayments()
    {
        return $this->hasMany(PagoFacilPayment::className(), [
            'pago_facil_transmition_file_pago_facil_transmition_file_id' => 'pago_facil_transmition_file_id'
        ]);
    }
    
    public function payments(){
        return $this->getPayments();
    }
    
    public function confirmFile(){
        $payments = $this->payments;
        
        foreach ($payments as $payment){
            $payment->paymentPayment->close();           
        }
        
        $this->status= 'closed';
        $this->updateAttributes(['status']);
        return true;
    }

}
