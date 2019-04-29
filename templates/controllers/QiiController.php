<?php

namespace app\templates\controllers;

/**
 * 
 * Description of QiiController
 * Overrides gii's DefaultController, and adds ajax functionality for rendering model and crud options
 * 
 * @author smaldonado
 * 
 */
class QiiController extends \yii\gii\controllers\DefaultController{
    
    const MODEL_ID = 'model';
    const CRUD_ID = 'crud';
    
    /**
     * @brief Gets a _POST form with the table name, and gets available relations from model generators.
     * @echo $json with status and thml
     */
    public function actionParseModelRelations(){
                
        $json = [];
                
        //Cargamos generador de Model
        $generator = $this->loadGenerator(self::MODEL_ID);
        
        //Validamos nombre de tabla
        $generator->validateTableName();
        if(empty($generator->getErrors())){
            
            $relations = $generator->generateRelations();
            
            if(isset($relations[$generator->tableName])){
                
                $json['html'] = $this->renderPartial('//../templates/views/_model_relations_form', [
                    'relations' => $relations[$generator->tableName],
                    'table' => $generator->tableName,
                    'generator' => $generator,
                ]);
                
            }
            
            $json['status'] = 'success';
            
        }else{
            
            //Errores
            $json['status'] = 'error';
            $json['html'] = $this->renderPartial('//../templates/views/_error', [
                'errorGroups' => $generator->getErrors(),
            ]);
            
        }
        
        echo \yii\helpers\Json::encode($json);
        
    }
    
    /**
     * @brief Gets a _POST form with the table name, and gets available relations from model generator.
     */
    public function actionParseCrudRelations(){
                
        $json = [];
                
        //Cargamos generador de Model
        $generator = $this->loadGenerator(self::CRUD_ID);
                
        //Validamos nombre de tabla    
        $generator->getModelRelations();
        if(empty($generator->getErrors())){
            
            if(isset($generator->relations)){
                
                $json['html'] = $this->renderPartial('//../templates/views/_crud_relations_form', [
                    'relations' => $generator->relations,
                    'table' => $generator->tableSchema->name,
                    'generator' => $generator,
                ]);
                
            }
            
            $json['status'] = 'success';
            
        }else{
            
            //Errores
            $json['status'] = 'error';
            $json['html'] = $this->renderPartial('//../templates/views/_error', [
                'errorGroups' => $generator->getErrors(),
            ]);
            
        }
        
        echo \yii\helpers\Json::encode($json);        
        
    }
    
    
}
