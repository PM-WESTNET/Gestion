<?php

namespace app\modules\import\controllers;

use Yii;
use app\components\web\Controller;
use app\modules\import\models\ImportModel;

class ImporterController extends Controller
{
    
    public function behaviors() {
        return [
            'access'=> [
                'class' => 'webvimark\modules\UserManagement\components\GhostAccessControl',
            ],
        ];
    }
    
    public function actionImport()
    {
        
        //Obtenemos la instancia del importador (singleton)
        $importerFactory = \app\modules\import\components\ImporterFactory::getInstance();
        
        //Obtenemos una lista de importadores
        $importers = $importerFactory->getImporters();
        
        $model = new ImportModel;
        
        if($model->load(Yii::$app->request->post()) && $model->validate()){
            //Cargamos el archivo a importar
            $file = \yii\web\UploadedFile::getInstance($model, 'file');
            
            //Obtenemos el importador seleccionado por el usuario
            $importer = $importerFactory->getImporter($model->model);
            
            //Ejecutamos la importacion
            $count = $importer->import($file);
            
            //Mostramos un mensaje de exito
            Yii::$app->getSession()->setFlash('success', Yii::t('import','{count} {modelClass} has been imported.', ['count'=>$count, 'modelClass'=>Yii::t('app','Products')]));
            
            //Redireccionamos a la lista de productos
            return $this->redirect(['/sale/product/index']);
        }
        
        return $this->render('import', ['importers'=>$importers, 'model'=>$model]);
    }
}
