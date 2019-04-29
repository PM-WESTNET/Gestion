<?php

namespace app\modules\ticket\controllers;

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

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

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

}
