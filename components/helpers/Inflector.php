<?php

namespace app\components\helpers;

use Yii;

/**
 * Adapta \ICanBoogie\Inflector a ciertas particularidades del español
 *
 * @author martin
 */
class Inflector extends \ICanBoogie\Inflector {
    
    static $language;
    
    /**
     * Devuelve un inflector de acuerdo al lenguaje configurado
     * @return self
     */
    public static function getInflector()
    {
        
        self::$language = Yii::$app->language;
        self::$language = substr(self::$language, 0, 2);
        
        return parent::get(self::$language);
        
    }
    
    /**
     * Hay que analizar mas casos, pero en general en palabras compuestas
     * en español, se pluraliza la primer palabra o se puede aplicar una 
     * version que solo requiera pluralizar la primer palabra. Por ejemplo,
     * en lugar de "Casas Blancas", podemos emplear "Casas color Blanco".
     * @param type $word
     * @return type
     */
    public function pluralize($word) {
        if(self::$language == 'es'){
            $words = explode(' ', $word);
            $words[0] = parent::pluralize($words[0]);
            
            return implode(' ', $words);
        }else{
            return parent::pluralize($word);
        }
    }
    
    /**
     * En español, singularizamos cada palabra en caso de palabras compuestas.
     * @param type $word
     * @return type
     */
    public function singularize($word) {
        if(self::$language == 'es'){
            $words = explode(' ', $word);
            foreach($words as $i=>$w){
                $words[$i] = parent::singularize($w);
            }
            
            return implode(' ', $words);
        }else{
            return parent::singularize($word);
        }
    }
        
}
