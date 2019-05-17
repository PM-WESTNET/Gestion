<?php

namespace app\modules\cobrodigital\controllers;

use Yii;
use app\modules\cobrodigital\models\PaymentCard;
use app\modules\cobrodigital\models\search\PaymentCardSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * PaymentCardController implements the CRUD actions for PaymentCard model.
 */
class PaymentCardController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all PaymentCard models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PaymentCardSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PaymentCard model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the PaymentCard model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PaymentCard the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PaymentCard::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * @return array
     * Indica la cantidad de tarjetas de cobro que hay sin usar
     */
    public function actionGetUnusedPaymnetCardsQty()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $qty = PaymentCard::find()->where(['used' => 0])->count();

        return [
            'status' => 'success',
            'qty' => $qty
        ];
    }
}
