<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 5/08/16
 * Time: 12:39
 */

namespace app\modules\sale\components\CodeGenerator;


/**
 * Interface CodeGeneratorInterface
 *
 * @package app\modules\sale\components\CodeGenerator
 */
interface CodeGeneratorInterface
{
    /**
     * Genera el codigo
     * @param $code
     * @return string
     */
    public function generate($code);

    /**
     * Valida el codigo enviado
     * @param $code
     * @return bool
     */
    public function validate($code);
}