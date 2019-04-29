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
use app\modules\accounting\models\ResumeItem;
use app\modules\import\components\AbstractCsvImport;
use PHPExcel;
use PHPExcel_Cell_DataValidation;
use PHPExcel_IOFactory;
use Yii;
use yii\db\Expression;

class ResumeImporter extends AbstractCsvImport
{
    public function getValueFunctions()
    {
        return [
            'Código' => function($value) {
                $query = MoneyBoxHasOperationType::find();
                $value = $query->select('money_box_has_operation_type_id')
                    ->leftJoin('operation_type ot', 'money_box_has_operation_type.operation_type_id = ot.operation_type_id')
                    ->where([
                        'money_box_id'=>$this->_params['money_box_id'],
                        'ot.code'=>$value,
                    ])->andWhere(new Expression('money_box_account_id ='.$this->_params['money_box_account_id'] . ' or money_box_account_id is null'  ))
                ->one();

                return $value['money_box_has_operation_type_id'];
            }
        ];
    }

    public function persist($data)
    {
        $item = new ResumeItem();
        try {
            $item->setAttributes([
                'description' => $data['Descripcion'],
                'reference' => '',
                'code' => $data['Código'],
                'debit' => $data['Débito'],
                'credit' => $data['Crédito'],
                'status' => ResumeItem::STATE_DRAFT,
                'date' => Yii::$app->formatter->asDate(str_replace('/','-', $data['Fecha'])),
                'money_box_has_operation_type_id' => $data['Código-new'],
                'resume_id' => $this->_params['resume_id']
            ]);
            if(!$item->validate()) {
                $item->save(false);
            } else {
                foreach( $item->getErrors() as $value) {
                    $this->_errors[] = $value;
                }

                throw new \Exception(Yii::t('app', 'Validation error.'));
            }
        } catch(\Exception $ex) {
            throw new \Exception(Yii::t('app', 'Error on save. ' . $ex->getMessage()));
        }
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
}
