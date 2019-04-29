<?php

namespace app\modules\afip\models;

use app\components\helpers\EmptyLogger;
use app\components\workflow\WithWorkflow;
use app\modules\sale\models\search\BillSearch;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "taxes_book".
 *
 * @property integer $taxes_book_id
 * @property string $type
 * @property string $status
 * @property string $timestamp
 * @property string $number
 * @property integer $company_id
 *
 * @property TaxesBookItem[] $taxesBookItems
 */
class TaxesBook extends \app\components\companies\ActiveRecord
{

    use WithWorkflow;

    const STATE_DRAFT       = 'draft';
    const STATE_CLOSED      = 'closed';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'taxes_book';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['timestamp'],
                ],
                'value' => function(){return time();}
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['type', 'period'], 'required'],
            [['type', 'status', 'number'], 'string'],
            [['type'], 'in', 'range' => ['buy', 'sale']],
            [['status'], 'in', 'range' => ['draft', 'closed']],
            [['status'], 'default', 'value'=>'draft'],
            [['timestamp'], 'safe'],
        ];
        if (Yii::$app->params['companies']['enabled']) {
            $rules[] = [['company_id'], 'integer'];
        }
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'taxes_book_id' => Yii::t('app', 'Taxes Book ID'),
            'type' => Yii::t('afip', 'Type'),
            'period' => Yii::t('afip', 'Period'),
            'status' => Yii::t('app', 'Status'),
            'timestamp' => Yii::t('app', 'Timestamp'),
            'number' => Yii::t('app', 'Number'),
            'company_id' => Yii::t('app', 'Company ID'),
            'taxesBookItems' => Yii::t('afip', 'TaxesBookItems'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxesBookItems()
    {
        return $this->hasMany(TaxesBookItem::className(), ['taxes_book_id' => 'taxes_book_id']);
    }

    /**
     * @inheritdoc
     */

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->formatDatesBeforeSave();
            return true;
        } else {
            return false;
        }
    }
             
    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        return $this->status==TaxesBook::STATE_DRAFT;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: TaxesBookItems.
     */
    protected function unlinkWeakRelations(){
        $this->unlinkAll('taxesBookItems', true);
    }
    
    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if($this->getDeletable()){
                $this->deleteTaxesBookItems();
                $this->unlinkWeakRelations();
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     * Elimina todos los item que esten relacionados con el libro de IVA
     */
    public function deleteTaxesBookItems()
    {
        foreach ($this->taxesBookItems as $item) {
            $item->delete();
        }
    }

    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave()
    {
        try {
            $this->period = Yii::$app->formatter->asDate($this->period, 'yyyy-MM-dd');
        } catch(\Exception $ex) {
            $this->period = Yii::$app->formatter->asDate("01-".$this->period, 'yyyy-MM-dd');
        }
    }

    /**
     * @brief Format dates using formatter local configuration
     */
    private function formatDatesAfterFind()
    {
        $this->period = Yii::$app->formatter->asDate($this->period);
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        $this->formatDatesAfterFind();
        parent::afterFind();
    }

    /**
     * Retorna el atributo que maneja el estado del objeto para el workflow.
     *
     * @return mixed
     */
    public function getWorkflowAttr()
    {
        return "status";
    }

    /**
     * Retorna los estados.
     *
     * @return mixed
     */
    public function getWorkflowStates()
    {
        return [
            self::STATE_DRAFT => [
                self::STATE_CLOSED
            ]
        ];
    }

    /**
     * Se implementa en el caso que se quiera crear un log de estados.
     * @return mixed
     */
    public function getWorkflowCreateLog(){}


    public function can($new_state)
    {
        $period = new \DateTime((new \DateTime($this->period))->format('Y-m-t'));
        $now = new \DateTime('now');
        return (WithWorkflow::can($new_state)  && ($period->diff($now)->format('%R%a') >= 0) );
    }

    /**
     * @return bool
     * @throws \yii\db\Exception
     * Cierra el libro de IVA
     */
    public function close()
    {
        Yii::setLogger(new EmptyLogger());
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($this->type =='sale') {
                $this->deleteTaxesBookItems();
                $this->updateItemsSaleBook();
            } else {
                $this->updateItemsBuyBook();
            }
            $this->status = TaxesBook::STATE_CLOSED;
            $this->save();
            $transaction->commit();
            return true;
        } catch (\Exception $ex) {
            $transaction->rollBack();
            return false;
        }
    }

    /**
     * @return bool
     * @throws \yii\db\Exception
     * Guarda los items del libro de IVA Ventas, persistiendo el status en draft.
     */
    public function saveItems()
    {
        Yii::setLogger(new EmptyLogger());
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->deleteTaxesBookItems();
            $this->updateItemsSaleBook();
            $this->status = TaxesBook::STATE_DRAFT;
            $this->save();
            $transaction->commit();
            return true;
        } catch (\Exception $ex) {
            $transaction->rollBack();
            return false;
            return false;
        }
    }

    /**
     * Añade los items que corresponden al libro de IVA ventas.
     */
    private function updateItemsSaleBook()
    {
        $searchModel = new BillSearch([
            'fromDate' => $this->period,
            'toDate' => (new \DateTime($this->period))->format('Y-m-t'),
            'status' => 'closed',
            'company_id' => $this->company_id,
            'bill_types' => ArrayHelper::getColumn( $this->company->billTypes, 'bill_type_id'),
        ]);

        $dataProvider = $searchModel->search([]);
        $bills = $dataProvider->query->all();
        $lastPage = $this->getNextPage();
        $i = 0;
        foreach($bills as $key=>$bill) {
            $this->addTaxesBookItem($bill->bill_id, $lastPage);
            $i++;
            if(($i%30)==0) {
                $lastPage++;
            }
        }
    }

    /**
     * Añade los items que corresponden al libro de IVA compras.
     */
    private function updateItemsBuyBook()
    {
        $lastPage = $this->getNextPage();
        $i = 0;

        $items = $this->getTaxesBookItems()
            ->leftJoin('provider_bill pb', 'taxes_book_item.provider_bill_id = pb.provider_bill_id' )
            ->orderBy(['pb.date'=>SORT_ASC])->all();

        foreach ($items as $item) {
            $item->page = $lastPage;
            $item->save();
            $i++;
            if(($i%30)==0) {
                $lastPage++;
            }
        }
    }

    /**
     * Retorno la siguiente pagina a agregar.
     *
     */
    public function getNextPage()
    {
        return (new Query())
            ->select([new Expression('coalesce(max(page),0) + 1')])
            ->from('taxes_book tb')
            ->leftJoin('taxes_book_item tbi', 'tb.taxes_book_id = tbi.taxes_book_id' )
            ->where(['tb.company_id'=>$this->company_id, 'tb.type'=>$this->type])
            ->scalar();
        ;
    }

    /**
     * @param $bill_id Id del comprobante
     * @param $page Número de página
     * @return bool
     * Agrega un item al libro de Iva
     */
    public function addTaxesBookItem($bill_id, $page)
    {
        $item = new TaxesBookItem([
            'page' => $page,
            'bill_id' => $bill_id,
            'taxes_book_id' => $this->taxes_book_id
        ]);

        if($item->save()){
            return true;
        }else{
            Debug::debug($item->getErrors());
        }

        return false;
    }

}
