<?php

namespace app\modules\import\components;

use yii\helpers\FileHelper;

/**
 * Description of ImporterFactory
 *
 * @author martin
 */
class ImporterFactory {
    
    private static $instance;
    
    private $importers = [];
    
    public static function getInstance()
    {
        if(empty(self::$instance)){
            self::$instance = new ImporterFactory;
        }
        return self::$instance;
    }
    
    public function getImporters(){
        
        if(empty($this->importers)){
            $dirs = scandir(\Yii::getAlias('@app').'/modules/import/components/importers');

            $dirs = array_diff($dirs, array('.','..'));

            foreach($dirs as $importer){
                $this->importers[$importer] = $this->getImporter($importer);
            }
        }
        
        return $this->importers;
        
    }
    
    public function getImporter($id){
        
        $className = $this->getImporterName($id);
        
        //Si la clase no ha sido cargada aun
        if(!class_exists($className)){
            require(__DIR__."/importers/$id/$className.php");
        }
        
        return \Yii::createObject($className);
        
    }
    
    public function getImporterName($id){
        
        return ucfirst($id).'Importer';
        
    }
    
    private function loadImporter($id){
        
        $className = $this->getImporterName($id);
        require (__DIR__."importers/$id/$className");
        
    }
    
}
