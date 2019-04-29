<?php

namespace app\modules\media\controllers;

use Yii;
use app\modules\media\models\types\Image;
use app\modules\media\models\types\search\ImageSearch;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ImageController implements the CRUD actions for Image model.
 */
class ImageController extends DefaultController
{

    /**
     * Displays a single Image model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Image model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $model = new Image();
        $model->load(Yii::$app->request->post('Media'));
        
        if (Yii::$app->request->isPost && $model->upload()) {
            
            if($model->save()){
                
                $preview = \app\modules\media\components\view\Preview::widget([
                    'update' => true,
                    'media' => $model,
                ]);
                
                return [
                    'status' => 'success',
                    'model' => $model,
                    'preview' => $preview
                ];
            }
            
        } 
            
        return [
            'status' => 'error',
            'errors' => $model->getErrors(),
        ];
            
    }

    /**
     * Updates an existing Image model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->media_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Image model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

}
