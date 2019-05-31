<?php

namespace app\modules\checkout\controllers;

use app\modules\checkout\models\CompanyHasPaymentTrack;
use app\modules\checkout\models\PaymentMethod;
use app\modules\checkout\models\Track;
use app\modules\sale\models\Company;
use Yii;
use app\modules\checkout\models\CompanyHasPaymentTrackSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CompanyHasPaymentTrackController implements the CRUD actions for CustomerHasPaymentTrack model.
 */
class CompanyHasPaymentTrackController extends Controller
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
     * Lists all CustomerHasPaymentTrack models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CompanyHasPaymentTrackSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CustomerHasPaymentTrack model.
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
     * Creates a new CustomerHasPaymentTrack model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CustomerHasPaymentTrack();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->customer_has_payment_track]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing CustomerHasPaymentTrack model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->customer_has_payment_track]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing CustomerHasPaymentTrack model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the CustomerHasPaymentTrack model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CustomerHasPaymentTrack the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CustomerHasPaymentTrack::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    protected function findCompany($id)
    {
        if (($model = Company::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionPaymentMethods($company_id) {

        $company = $this->findCompany($company_id);
        $payment_methods = $company->paymentTracks;
        $payment_track_exists = true;

        if(!$payment_methods) {
            $payment_track_exists = false;
            $payment_methods = PaymentMethod::getAllowedAndEnabledPaymentMethods($company_id);
        }

        if(array_key_exists('CompanyHasPaymentTrack', Yii::$app->request->post())) {
            //Eliminar relaciones existentes
            if(!$payment_track_exists) {
                foreach ($company->paymentTracks as $paymentTrack) {
                    $paymentTrack->delete();
                }
            }

            foreach ($payment_methods as $payment_method) {
                foreach (Track::find()->all() as $track) {
                    $payment_status = CompanyHasPaymentTrack::STATUS_DISABLED;
                    if(array_key_exists('payment_status', Yii::$app->request->post('CompanyHasPaymentTrack'))) {
                        $payment_status = array_key_exists($payment_method->payment_method_id, Yii::$app->request->post('CompanyHasPaymentTrack')['payment_status']) ? CompanyHasPaymentTrack::STATUS_ENABLED : CompanyHasPaymentTrack::STATUS_DISABLED;
                    }

                    $customer_status = CompanyHasPaymentTrack::STATUS_DISABLED;
                    if(array_key_exists('customer_status', Yii::$app->request->post('CompanyHasPaymentTrack'))) {
                        $customer_status = array_key_exists($payment_method->payment_method_id, Yii::$app->request->post('CompanyHasPaymentTrack')['customer_status']) ? CompanyHasPaymentTrack::STATUS_ENABLED : CompanyHasPaymentTrack::STATUS_DISABLED;
                    }

                    if(!$payment_track_exists) {
                        $company_has_payment_track = new CompanyHasPaymentTrack([
                            'company_id' => $company_id,
                            'payment_method_id' => $payment_method->payment_method_id,
                            'track_id' => $track->track_id,
                            'payment_status' => $payment_status,
                            'customer_status' => $customer_status
                        ]);
                        $company_has_payment_track->save();
                    } else {
                        $payment_method->updateAttributes(['payment_status' => $payment_status, 'customer_status' => $customer_status]);
                    }
                }
            }

            return $this->redirect(['tracks',
                'company_id' => $company_id,
            ]);
        }


        return $this->render('payment-methods', [
            'payment_methods' => $payment_methods,
            'model' => $company
        ]);
    }


    public function actionTracks($company_id) {
        $company = $this->findCompany($company_id);

        $tracks = Track::find()->all();

        if(array_key_exists('CompanyHasPaymentTrack', Yii::$app->request->post())) {

            foreach ($company->paymentTracks as $payment_track) {
                $track_status = CompanyHasPaymentTrack::STATUS_DISABLED;

                if(array_key_exists('track_status', Yii::$app->request->post('CompanyHasPaymentTrack'))) {
                    if(array_key_exists($payment_track->track_id, Yii::$app->request->post('CompanyHasPaymentTrack')['track_status'])) {
                        $track_status = CompanyHasPaymentTrack::STATUS_ENABLED;
                    }
                }

                $payment_track->updateAttributes(['track_status' => $track_status]);
            }

            return $this->redirect(['payment-tracks-and-default',
                'company_id' => $company_id,
            ]);
        }

        return $this->render('tracks', [
            'model' => $company,
            'tracks' => $tracks
        ]);
    }

    public function actionPaymentTracksAndDefault($company_id) {
        $company = $this->findCompany($company_id);

        if(array_key_exists('CompanyHasPaymentTrack', Yii::$app->request->post())) {
            foreach ($company->paymentTracks as $payment_track) {

                $payment_track_status = CompanyHasPaymentTrack::STATUS_DISABLED;
                $default_track = false;

                if(array_key_exists($payment_track->payment_method_id, Yii::$app->request->post('CompanyHasPaymentTrack'))) {
                    $payment_array = Yii::$app->request->post('CompanyHasPaymentTrack')[$payment_track->payment_method_id];
                    $payment_track_status_array = array_key_exists('payment_track_status', $payment_array) ? $payment_array['payment_track_status'] : [];
                    $default_track_array = array_key_exists('default_track', $payment_array) ? $payment_array['default_track'] : [];

                    if(array_key_exists($payment_track->track_id, $payment_track_status_array)) {
                        $payment_track_status = CompanyHasPaymentTrack::STATUS_ENABLED;
                    }

                    if(array_key_exists($payment_track->track_id, $default_track_array)) {
                        $default_track = true;
                    }
                }

                $payment_track->updateAttributes(['payment_track_status' => $payment_track_status, 'default_track' => $default_track]);
            }

            return $this->redirect(['/sale/company/view',
                    'id' => $company_id
                ]);
        }

        $paymentTracks = $company->getPaymentTracks()->where(['payment_status' => CompanyHasPaymentTrack::STATUS_ENABLED, 'track_status' => CompanyHasPaymentTrack::STATUS_ENABLED])->all();

        return $this->render('payment-tracks-and-default', [
            'model' => $company,
            'paymentTracks' => $paymentTracks
        ]);
    }
}
