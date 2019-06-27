<?php

namespace app\modules\ticket\controllers;

use app\modules\ticket\models\Observation;
use yii\web\Response;
use Yii;

class ObservationController extends \app\components\web\Controller {

    public function actionIndex() {
        return $this->render('index');
    }

    /**
     * @brief Devuelve un json con html para renderizar un evento de nota con nombre de usuario, fecha y contenido de la nota
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionBuildObservation() {

        \Yii::$app->response->format = Response::FORMAT_JSON;

        $json = [];

        if ($post = \Yii::$app->request->post()) {

            $username = $post['username'];
            $title = $post['title'];
            $body = $post['body'];
            $time = time();

            if (!empty($username) && !empty($body)) {

                $json['status'] = 'success';
                $json['html'] = $this->renderAjax('build_observation', [
                    'username' => $username,
                    'title' => $title,
                    'body' => $body,
                    'time' => $time,
                ]);
            } else {
                $json['status'] = 'error';
            }

            return $json;
        } else {

            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @return array
     * Crea una observacion y devuelve una respuesta json
     */
    public function actionCreate()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new Observation();
        if($model->load(Yii::$app->request->post()) && $model->save()){
            return [
                'status' => 'success',
                'observation' => $model,
                'errors' => []
            ];
        }

        return [
            'status' => 'error',
            'observation' => $model,
            'errors' => $model->getErrors()
        ];

    }
}
