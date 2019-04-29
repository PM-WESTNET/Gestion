<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 3/05/16
 * Time: 15:37
 */

namespace app\modules\import\components;
use Yii;

/**
 * Class AbstractCsvImport
 * Clase abstract para la importación de registros via CSV.
 * Se debe implementar dos metodos,
 *      persist:
 *              será la funcion encargada de guarar en la base de datos el registro.
 *
 *      getValueFunctions:
 *              tiene que retornar un array vacio o un array con funciones que reciban un parametro. Este array
 * tiene que tener como key el nombre del campo. Cada una de estas funciones debera retornar el valor transformado
 * para el campo en cuestion.
 *
 * @package app\modules\import\components
 */
abstract class AbstractCsvImport
{
    /**
     * Array de las columnas
     * @var array
     */
    protected $_columns;
    /**
     * Nombre del archivo a importar
     * @var string
     */
    protected $_fileName;
    /**
     * Handler del archivo a importar.
     * @var resource
     */
    protected $_file;

    /**
     * Separador de campos
     * @var string
     */
    protected $_separator;

    /**
     * Parametros adicionales
     * @var array
     */
    protected $_params;

    /**
     * Array con errores
     * @var array
     */
    protected $_errors;

    /**
     * Si el proceso completo se realiza dentro de una transaccion
     * @var bool
     */
    protected $_inTransaction = true;

    /**
     * AbstractCsvImport constructor.
     * @param $fileName
     * @param $columns
     * @param string $separator
     */
    public function __construct($fileName, $columns, $separator = ';')
    {
        $this->_fileName = $fileName;
        $this->_columns = $columns;
        $this->_separator = $separator;
    }

    /**
     * Inicializa campos y archivo. Recibe como parametro un array con parametros
     * adicionales para la importación.
     *
     * @param $params array
     * @return $this
     * @throws \Exception
     */
    public function init($params=array())
    {
        if (!is_array($this->_columns)) {
            $this->_columns = explode($this->_separator, $this->_columns);
        }
        if (is_file($this->_fileName)) {
            $this->_file = fopen($this->_fileName, 'r');
        } else {
            throw new \Exception('The selected file not exist or is invalid.');
        }
        $this->_params = $params;

        return $this;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * @param array $errors
     * @return AbstractCsvImport
     */
    public function setErrors($errors)
    {
        $this->_errors = $errors;
        return $this;
    }



    /**
     * Retorna un array con las funciones para obtener valores para los campos necesarios.
     * El formato tiene que ser:
     *  - [
     *      'Codigo' => function($data) {
     *              return $data . " - Dato transformado";
     *      }
     * ]
     * @return mixed
     */
    public abstract function getValueFunctions();

    /**
     * Persiste el registro en la base de datos.
     *
     * @param $data
     * @return mixed
     */
    public abstract function persist($data);

    /**
     * Ejecuta la importacion completa.
     */
    public function import($inTransaction=true)
    {
        if ($this->_file) {
            $row = 0;
            $col = 0;
            $header = false;
            $valueFunctions = $this->getValueFunctions();

            if($inTransaction) {
                $transaction = Yii::$app->db->beginTransaction();
            }

            while (($row = fgetcsv($this->_file, null, $this->_separator)) !== false) {
                // Si es la cabecera la paso de largo
                if (!$header) {
                    $header = true;
                    continue;
                }

                Yii::debug('Fila: '.print_r($row, 1), 'Conciliacion');
                Yii::debug('Columnas: '.print_r($this->_columns, 1), 'Conciliacion');

                $data = [];
                foreach ($row as $k => $value) {
                    $fieldName = $this->_columns[$k];
                    if (array_key_exists($fieldName, $valueFunctions)!==false) {
                        $data[$fieldName."-new"] = utf8_decode($valueFunctions[$fieldName]($value));
                    }
                    $data[$fieldName] = utf8_decode($value);
                }
                try {
                    if (!$this->persist($data)) {
                        $transaction->rollBack();
                        return false;
                    }
                } catch(\Exception $ex) {
                    $this->_errors[] = $ex->getMessage();
                    if($inTransaction) {
                        $transaction->rollBack();
                        return false;
                    }
                    break;
                }
                $col++;
                unset($row);
                unset($data);
            }
            if($inTransaction) {
                $transaction->commit();
            }
        }
        if(is_resource($this->_file)) {
            fclose($this->_file);
        }
        return true;
    }
}