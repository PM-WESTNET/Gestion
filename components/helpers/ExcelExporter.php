<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 30/09/16
 * Time: 15:36
 */

namespace app\components\helpers;


use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_NumberFormat;

class ExcelExporter
{
    /** @var int $row */
    private $row = 1;

    /** @var PHPExcel $excel */
    private $excel = null;

    /** @var array $columns */
    private $columns = [];

    /** @var ExcelExporter $instance */
    private static $instance = null;

    /**
     * @return ExcelExporter
     */
    public static function getInstance()
    {
        if(!self::$instance) {
            self::$instance = new ExcelExporter();
        }
        return self::$instance;
    }

    /**
     * @return int
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * @param int $row
     */
    public function setRow($row)
    {
        $this->row = $row;
    }


    /**
     * Pone cabeceras y permite bajar el excel.
     *
     * @param $fileName
     */
    public function download($fileName, $clear_headers = true)
    {
        //Se agrega ob_end_clean() para evitar error de "Headers already sent"
        if($clear_headers){
            ob_end_clean();
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }

    /**
     * Crea el archivo excel.
     *
     * @param string $title
     * @param array $columns Arreglo con Columna, nombre y formato.
     * [
     *  'A'=> [
     *      'Nombre',
     *      PHPExcel_Style_NumberFormat::FORMAT_TEXT
     * ]
     *
     * @return $this
     */
    public function create($title = "", $columns = [])
    {
        $this->excel = new PHPExcel();
        $this->excel->getProperties()
            ->setCreator("Arya By Quoma S.A.")
            ->setTitle($title);

        $this->columns = $columns;

        return $this;
    }

    /**
     * @param int $sheet
     */
    private $rrr=0;
    public function createHeader($sheet = 0)
    {
        if(!empty($this->columns)) {
            $this->excel->setActiveSheetIndex($sheet);
            $sheet = $this->excel->getActiveSheet();
            $col = 0;
            foreach ($this->columns as $column=>$value) {
                $sheet->setCellValueByColumnAndRow($col, $this->row, $value[1] );
                $sheet->getStyle($column)
                    ->getNumberFormat()
                    ->setFormatCode($value[2]);

                $col++;
            }
            $this->row++;
        }
        return $this;
    }

    /**
     * Escribe una linea
     * @param $data
     * @param null $cell
     * @return $this
     * @throws \Exception
     */
    public function writeRow($data, $sheet = 0, $useColumns = true)
    {
        if(!$this->excel) {
            throw new \Exception('The excel is null.');
        }

        $this->excel->setActiveSheetIndex($sheet);

        $sheet = $this->excel->getActiveSheet();

        if(empty($this->columns) || !$useColumns) {
            foreach ($data as $column=>$value) {
                $sheet->setCellValueByColumnAndRow($column, $this->row, $value )->getStyle()->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            }
        } else {
            foreach($this->columns as $col=>$value) {
                $sheet->setCellValue($col.$this->row, $data[$value[0]] );
            }
        }

        $this->row++;

        return $this;
    }

    public function writeData($data, $sheet = 0)
    {
        foreach($data as $value) {
            $this->writeRow($value, $sheet);
        }

        return $this;
    }
}