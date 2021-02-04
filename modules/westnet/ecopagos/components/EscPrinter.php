<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 13/07/16
 * Time: 13:19
 */

namespace app\modules\westnet\ecopagos\components;


class EscPrinter
{
    private $buffer = '';


    const NUL = "\x00";
    const LF = "\x0a";
    const ESC = "\x1b";
    const FS = "\x1c";
    const FF = "\x0c";
    const GS = "\x1d";
    const DLE = "\x10";
    const EOT = "\x04";
    const CR = "\x0d";

    /**
     * @var string The input encoding of the buffer.
     */
    const INPUT_ENCODING = "UTF-8";
    /**
     * @var string Un-recorgnised characters will be replaced with this.
     */
    const REPLACEMENT_CHAR = "?";

    public function clearBuffer() {
        $this->buffer = '';
    }

    public function getBuffer(){
        return self::ESC ."@".  $this->buffer;
    }

    /**
     * Print and feed line / Print and feed n lines.
     *
     * @param int $lines Number of lines to feed
     */
    public function feed($lines = 1) {
        for($i=1; $i <= $lines;$i++) {
            $this->buffer .= self::LF ;
        }
    }

    /**
     * Print and feed line / Print and feed n lines.
     *
     * @param int $lines Number of lines to feed
     */
    public function reverseFeed($lines = 1) {
        for($i=1; $i <= $lines;$i++) {
            $this->buffer .= self::ESC . "K" . chr($lines);
        }
    }

    public function writeText($text) {
        if($text == null) {
            return;
        }
        if(!mb_detect_encoding($text, self::INPUT_ENCODING, true)) {
            // Assume that the user has already put non-UTF8 into the target encoding.
            $this->buffer .= $this->writeTextRaw($text);
        }
        $i = 0;
        $j = 0;
        $len = mb_strlen($text, self::INPUT_ENCODING);
        while($i < $len) {
            $matching = true;
            $i++;
            $j = 1;
            do {
                $char = mb_substr($text, $i, 1, self::INPUT_ENCODING);
                $i++;
                $j++;
            } while($matching && $i < $len);
            $this->buffer .= mb_substr($text, $i - $j, $j, self::INPUT_ENCODING);
        }
    }

    public function writeTextRaw($text) {
        if(strlen($text) == 0) {
            return;
        }
        // Pass only printable characters
        for($i = 0; $i < strlen($text); $i++) {
            $c = substr($text, $i, 1);
            if(!self::asciiCheck($c, true)) {
                $text[$i] = self::REPLACEMENT_CHAR;
            }
        }
        return $text;
    }

    /**
     * Return true if a character is an ASCII printable character.
     *
     * @param string $char Character to check
     * @param boolean $extended True to allow 128-256 values also (excluded by default)
     * @return boolean True if the character is printable, false if it is not.
     */
    private static function asciiCheck($char, $extended = false) {
        if(strlen($char) != 1) {
            // Multi-byte string
            return false;
        }
        $num = ord($char);
        if($num > 31 && $num < 127) { // Printable
            return true;
        }
        if($num == 10) { // New-line (printer will take these)
            return true;
        }
        if($extended && $num > 127) {
            return true;
        }
        return false;
    }
}