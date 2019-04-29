<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 3/08/16
 * Time: 16:56
 */

namespace app\modules\sale\components\CodeGenerator\impl;


use app\modules\sale\components\CodeGenerator\CodeGeneratorInterface;

class PagoFacilCodeGenerator implements CodeGeneratorInterface
{

    // La secuencia es  1 3 5 7 9, y repite 3 5 7 9
    const SEQUENCE = [3,5,7,9];

    public function generate($code)
    {
        if(strlen($code) == 0) {
            return $code;
        } else {
            $firstDigit = $this->getDigit($code);
            $secondDigit = $this->getDigit($code.$firstDigit);
            return ((string)$code) . $firstDigit.$secondDigit;
        }
    }

    public function validate($code)
    {
        if(strlen($code)==0) {
            return false;
        }
        $codeN = substr($code, 0, strlen($code)-2);
        $firstDigit = $this->getDigit($codeN);
        $secondDigit = $this->getDigit($codeN.$firstDigit);

        return (substr($code, strlen($code)-2) == $firstDigit.$secondDigit );
    }

    private function getDigit($data)
    {
        $sum = 0;
        $data = (string)$data;
        // Multiplico el primer digito
        $sum = $data[0] * 1;
        // Hago el loop para el resto de los digitos
        $s = 0;
        for($i=1; $i<strlen($data); $i++) {
            if($i==0) break;
            $sum += $data[$i] * ( PagoFacilCodeGenerator::SEQUENCE[$s] );
            $s = ($s==3 ? 0 : $s + 1);
        }

        $digit = floor($sum/2);
        $digit = ($digit % 10);

        return (string)$digit;
    }
}