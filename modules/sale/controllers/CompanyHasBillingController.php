<?php

namespace app\modules\sale\controllers;

use app\modules\sale\models\BillType;
use app\modules\sale\models\Company;
use Yii;
use app\modules\sale\models\CompanyHasBilling;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CompanyHasBillingController implements the CRUD actions for CompanyHasBilling model.
 */
class CompanyHasBillingController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all CompanyHasBilling models.
     * @return mixed
     */
    public function actionIndex()
    {
        $types = [];
        $bill_types = CompanyHasBilling::find()->orderBy(['bill_type_id'=>SORT_ASC])->all();

        foreach ($bill_types as $bt) {
            $types[$bt['parent_company_id']][$bt['bill_type_id']] = $bt['company_id'];
        }

        return $this->render('index', [
            'types' => $types,
            'billTypes' => BillType::find()->all(),
            'parent_companies' => Company::find()->where("status = 'enabled' and parent_id is null")->all(),
            'companies' => Company::find()->where("status = 'enabled' and parent_id is not null")->all()
        ]);
    }

    /**
     * Guardo Todo
     *
     * @param integer $id
     * @return mixed
     */
    public function actionSave()
    {
        $chb = Yii::$app->request->post('chb');

        foreach ($chb as $parent_company_id => $values) {
            foreach ($values as $bill_type_id => $company_id)
            $this->save($parent_company_id, $company_id, $bill_type_id);
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the CompanyHasBilling model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CompanyHasBilling the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CompanyHasBilling::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function save($parent_company_id, $company_id, $bill_type_id)
    {
        if(($chb = CompanyHasBilling::findOne(['parent_company_id'=>$parent_company_id, 'bill_type_id'=>$bill_type_id]))!==null) {
            $chb->company_id = $company_id;
            $chb->updateAttributes(['company_id']);
        } else {
            $chb = new CompanyHasBilling();
            $chb->company_id = $company_id;
            $chb->parent_company_id = $parent_company_id;
            $chb->bill_type_id = $bill_type_id;
            $chb->save();
        }
    }
}