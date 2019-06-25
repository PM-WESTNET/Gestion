<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 11/06/18
 * Time: 11:48
 */

namespace app\modules\pagomiscuentas\models\search;

use app\modules\pagomiscuentas\models\PagomiscuentasFile;
use app\modules\sale\models\Bill;
use app\modules\sale\models\bills\Credit;
use app\modules\sale\models\BillType;
use Codeception\Util\Debug;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;

class PagomiscuentasFileSearch extends PagomiscuentasFile
{
    public $from;
    public $to;


    public function attributeLabels() {
        return array_merge([
            'from' => Yii::t('pagomiscuentas','From'),
            'to' => Yii::t('pagomiscuentas','To'),
        ], parent::attributeLabels());
    }

    public function findByFilter($params)
    {
        $this->load($params);

        /** @var ActiveQuery $query */
        $query = PagomiscuentasFile::find();

        $query->andFilterWhere(['company_id'=>$this->company_id]);
        $query->andFilterWhere(['type'=>$this->type]);

        if($this->from) {
            $query->andWhere(new Expression("date >= '" . $this->from->format('Y-m-d') ."'" ));
        }
        if($this->to) {
            $query->andWhere(new Expression("date <= '" . $this->to->format('Y-m-d') . "'"));
        }

        $query->orderBy(['date' => SORT_DESC ]);

        return $query;
    }


    public function findBills($pagomiscuentas_file_id)
    {
        $model = PagomiscuentasFile::findOne(['pagomiscuentas_file_id'=>$pagomiscuentas_file_id]);

        $credit_types = BillType::find()->andWhere(['class' => Credit::class])->all();

        $typesId= array_map(function ($model) { return $model->bill_type_id;}, $credit_types);

        $date = (new \DateTime($model->date))->format('Y-m-d');
        $fromDate= (new \DateTime($model->from_date))->format('Y-m-d');
        /** @var ActiveQuery $query */
        $query = (new Query());
        $query
            ->select("b.bill_id, b.total")
            ->from('bill b')
            ->leftJoin('pagomiscuentas_file_has_bill pfhb', 'b.bill_id = pfhb.bill_id')
            ->where('pfhb.bill_id is null')
            ->andWhere(['between', 'b.date', $fromDate, $date])
            ->andwhere(['b.company_id' => $model->company_id])
            ->andWhere(['b.status' => 'closed'])
            ->andWhere(['NOT IN', 'b.bill_type_id', $typesId])
            ->orderBy(['b.bill_id'=>SORT_DESC])
        ;

        return $query;
    }

    public function findBillsForExport($pagomiscuentas_file_id)
    {
        $pago_mis_cuentas_file = PagomiscuentasFile::findOne($pagomiscuentas_file_id);
        $vto = (new \DateTime($pago_mis_cuentas_file->date))->modify('last day of this month');
        $query = new Query();
        $query
            ->select(["c.code", "c.customer_id", "b.bill_id",
                new Expression( "'" . $vto->format('Ymd')."' as fecha_1_vto"),
                new Expression( "'" . $vto->format('Ymd')."' as fecha_2_vto"),
                new Expression( "'' as fecha_3_vto"),
                new Expression( "b.total as importe_1_vto"),
                new Expression( "b.total as importe_2_vto"),
                new Expression( "'' as importe_3_vto"),
                new Expression( "'Servicio de Internet' as detalle"),
                new Expression( "'' as barcode"),
        ])
            ->from("pagomiscuentas_file pf")
            ->leftJoin("pagomiscuentas_file_has_bill pfhb", "pf.pagomiscuentas_file_id = pfhb.pagomiscuentas_file_id")
            ->leftJoin("bill b", "pfhb.bill_id = b.bill_id")
            ->leftJoin('customer c', 'b.customer_id = c.customer_id')
            ->where(["pf.pagomiscuentas_file_id"=>$pagomiscuentas_file_id])
        ;


        return $query;
    }
}