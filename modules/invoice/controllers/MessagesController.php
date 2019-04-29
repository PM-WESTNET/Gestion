<?php

namespace app\modules\invoice\controllers;

use app\modules\invoice\models\MessageLog;
use yii\data\ActiveDataProvider;

class MessagesController extends \app\components\web\Controller
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => MessageLog::find()->orderBy(['message_log_id'=>SORT_DESC]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

}
