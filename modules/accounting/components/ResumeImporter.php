<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 4/05/16
 * Time: 11:35
 */

namespace app\modules\accounting\components;


use app\modules\accounting\models\MoneyBoxHasOperationType;
use app\modules\accounting\models\OperationType;
use app\modules\accounting\models\Resume;
use app\modules\accounting\models\ResumeItem;
use app\modules\import\components\AbstractCsvImport;
use Codeception\Util\Debug;
use PHPExcel;
use PHPExcel_Cell_DataValidation;
use PHPExcel_IOFactory;
use Yii;
use yii\db\Expression;

class ResumeImporter extends AbstractCsvImport
{
    /**@var Resume**/
    private $resume;

    public function getValueFunctions()
    {
        return [
            'Código' => function($value) {
                $query = MoneyBoxHasOperationType::find();
                $value = $query->select('money_box_has_operation_type_id')
                    //->leftJoin('operation_type ot', 'money_box_has_operation_type.operation_type_id = ot.operation_type_id')
                    ->where([
                        'money_box_id'=>$this->_params['money_box_id'],
                        'code'=>$value,
                    ])->andWhere(new Expression('money_box_account_id ='.$this->_params['money_box_account_id'] . ' or money_box_account_id is null'  ))
                ->one();

                return $value['money_box_has_operation_type_id'];
            }
        ];
    }

    public function persist($data)
    {
        /**
         * No siempre pero en algunos archivos sabe devolver la ultima fila vacia, lo que hace que despues falle, por eso
         * en este caso devuelvo true para que siga con el proceso
         */
        if (empty($data['Fecha']) && empty($data['Código'] && (empty($data['Crédito']) || empty($data['Débito'])))){
            return true;
        }

        /**
         * Si el tipo de operación no fue encontrada, se crea y se la asigna al money box del resumen
        **/
        if (empty($data['Código-new'])) {

            if (empty($this->resume)){
                $resume= Resume::findOne($this->_params['resume_id']);

                if (empty($resume)) {
                    $this->_errors[] = 'Resume not found';
                    return;
                }
                $this->resume = $resume;
            }

            /**$operation = new OperationType([
                'name' => 'Operation code :'. $data['Código'] ,
                'code' => $data['Código'],
                'is_debit' => $debit
            ]);



            if (!$operation->save()){
                Yii::debug(print_r($operation->getErrors(), 1));
                $this->_errors[] = Yii::t('accounting','Cant save operation with code {code}', ['code' => $data['Código']]);
                return;
            }**/

            $MBHOT= new MoneyBoxHasOperationType([
                'money_box_id' => $this->resume->moneyBoxAccount->money_box_id,
                'code' => $data['Código'],
                'money_box_account_id' => $this->_params['money_box_account_id'],
                'account_id' => $this->resume->moneyBoxAccount->account_id
            ]);

            $MBHOT->save();

            Yii::debug(print_r($MBHOT->getErrors(), 1));

            $data['Código-new'] = $MBHOT->money_box_has_operation_type_id ;
        }
        Yii::debug(print_r($data,1));
        $item = new ResumeItem();
        try {
            $item->setAttributes([
                'description' => $data['Descripcion'],
                'reference' => '',
                'code' => $data['Código'],
                'debit' => abs((double)($data['Debe'])),
                'credit' => abs((double)$data['Haber']),
                'status' => ResumeItem::STATE_DRAFT,
                'date' => $this->formatDate($data['Fecha']),
                'money_box_has_operation_type_id' => $data['Código-new'],
                'resume_id' => $this->_params['resume_id']
            ]);
            if($item->validate()) {
                $item->save(false);
            } else {
                Yii::debug(print_r($item->getErrors(), 1));
                foreach( $item->getErrors() as $value) {
                    $this->_errors[] = $value;
                }

                return false;
                //throw new \Exception(Yii::t('app', 'Validation error.'));
            }
        } catch(\Exception $ex) {
            Yii::debug($ex->getTraceAsString());
            throw new \Exception(Yii::t('app', 'Error on save. ' . $ex->getMessage()));
        }

        return true;
    }

    public function getImportExcel()
    {
        $excel = new PHPExcel();

        $excel->getProperties()
            ->setCreator("Arya By Quoma S.A.")
            ->setTitle("Importador de Resumenes");

        $excel
            ->setActiveSheetIndex(0)
            ->setCellValue('A1', Yii::t('app', 'Fecha'))
            ->setCellValue('B1', Yii::t('app', 'Código'))
            ->setCellValue('C1', Yii::t('app', 'Descripcion'))
            ->setCellValue('D1', Yii::t('app', 'Débito'))
            ->setCellValue('E1', Yii::t('app', 'Crédito'))

        ;
        $excel->getActiveSheet()->getColumnDimensionByColumn(1)->setWidth('50');
        $excel->getActiveSheet()->getColumnDimensionByColumn(1)->setAutoSize(false);
        $excel->getActiveSheet()->getColumnDimensionByColumn(2)->setWidth('50');
        $excel->getActiveSheet()->getColumnDimensionByColumn(2)->setAutoSize(false);
        // Creo los tipos de operacion.
        $excel->createSheet(1)->setTitle("Tipos_de_Operacion");
        $excel->setActiveSheetIndex(1)
            ->setCellValue('A1', Yii::t('app', 'Code'))
        ;

        $r = 2;
        $operations = OperationType::find()->all();
        foreach( $operations as $operation ) {
            $excel
                ->setActiveSheetIndex(1)
                ->setCellValue('A'.$r, $operation->code)
            ;
            $r++;
        }
        $excel->addNamedRange(new \PHPExcel_NamedRange('TIPO_OPERACION', $excel->getSheet(1), 'A2:A'.$r));

        $excel->setActiveSheetIndex(0);
        for($i=2; $i<500; $i++) {
            $objValidation = $excel->getActiveSheet()->getCell("B".$i)->getDataValidation();
            $objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
            $objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
            $objValidation->setAllowBlank(false);
            $objValidation->setShowInputMessage(true);
            $objValidation->setShowErrorMessage(true);
            $objValidation->setShowDropDown(true);
            $objValidation->setErrorTitle('Error');
            $objValidation->setError('El Valor no esta en la lista.');
            $objValidation->setPromptTitle('Seleccione desde la lista');
            $objValidation->setPrompt('Seleccione un Tipo de Operacion de la lista.');
            $objValidation->setFormula1('Tipos_de_Operacion!$A2:$A'.$r);
        }

        return PHPExcel_IOFactory::createWriter($excel, 'Excel5');

    }

    /**
     * Formatea la fecha que viene del archivo csv del banco. Algunos bancos expresan la fecha como dd/mm/yyyy otros como dd-mm-yyyy
     * y otros como en el caso del credicop mandan la fecha como yyyymmdd. Por lo que esta funcion normaliza la fecha y devuelve el
     * formato dd-mm-yyyy     *
     * @param $date
     * @return mixed|string
     */
    public function formatDate($date)
    {
        if (strpos($date, '/') !== false) {
            $date = str_replace('/', '-', $date);
            $explode = explode('-', $date);

            if ((integer)$explode[0] > 31){
                $date = Yii::$app->formatter->asDate($date, 'dd-MM-yyyy');
            }
        }
        elseif (strpos($date, '-') !== false) {
            $explode = explode('-', $date);

            if ((integer)$explode[0] > 31){
                $date = Yii::$app->formatter->asDate($date, 'dd-MM-yyyy');
            }
        }
        else {
            $first_block = substr($date,0, 4);
            $second_block = substr($date,4, 2);
            $thirth_block = substr($date,6);

            Debug::debug($thirth_block. '-'.$second_block.'-'.$first_block);

            if (!((integer)$second_block > 0 && (integer)$second_block < 13)) {
                $thirth_block = substr($date,0, 2);
                $second_block = substr($date,2, 2);
                $first_block = substr($date,4);

                Debug::debug($thirth_block. '-'.$second_block.'-'.$first_block);
            }

            $date = $thirth_block. '-'.$second_block.'-'.$first_block;
        }

        return $date;

    }
}
