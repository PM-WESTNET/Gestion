<?php

namespace app\modules\config\controllers;

use Yii;
use app\modules\config\models\Config;
use app\modules\config\models\search\ConfigSearch;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\config\models\Item;
use app\modules\config\ConfigModule;

/**
 * ConfigController implements the CRUD actions for Config model.
 */
class ConfigController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'reset' => ['post'],
                ],
            ],
        ]);
    }

    /**
     * Lists all Config models.
     * @return mixed
     */
    public function actionIndex($category)
    {
        $category = \app\modules\config\models\Category::find()->where(['category_id' => $category])->one();
        if($category === null){
            throw new \yii\web\HttpException(404, 'Category not found.');
        }
        
        if($data = Yii::$app->request->post()){
            
            $configs = [];
            
            unset($data['_csrf']);
            foreach($data as $attr => $value){
                $configs[] = Config::setValue($attr, $value);
            }
            
            return $this->render('index', [
                'models' => $configs,
                'category' => $category,
            ]);
        }
        
        $configs = [];
        $items = Item::getItems($category);
        foreach($items as $item){
            $configs[] = \app\modules\config\models\Config::getConfig($item->attr);
        }
        
        return $this->render('index', [
            'models' => $configs,
            'category' => $category,
        ]);
    }
    
    /**
     * Lists all Config models.
     * @return mixed
     */
    public function actionReset($category)
    {
        $category = \app\modules\config\models\Category::find()->where(['category_id' => $category])->one();
        if($category === null){
            throw new \yii\web\HttpException(404, 'Category not found.');
        }
        
        $category->resetValues();
        
        \Yii::$app->getSession()->setFlash('success', ConfigModule::t('config', 'All settings has been resetted to their default values.'));
        
        $this->redirect(['index', 'category' => $category->category_id]);
    }

    /**
     * Displays a single Config model.
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
     * Creates a new Config model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Config();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->config_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Config model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->config_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Config model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Config model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Config the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Config::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
