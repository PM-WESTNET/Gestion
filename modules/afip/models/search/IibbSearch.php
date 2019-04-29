<?php

namespace app\modules\afip\models\search;

use app\modules\afip\models\TaxesBook;
use app\modules\config\models\Config;
use app\modules\provider\models\ProviderBill;
use app\modules\sale\models\Bill;
use app\modules\sale\models\Product;
use app\modules\sale\models\TaxRate;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 25/08/15
 * Time: 9:31
 * @property
 */
class IibbSearch extends \app\components\companies\ActiveRecord
{

    public $fromDate;
    public $toDate;

    public $company_id;

    public $bill_types;

    public $products;

    public function rules()
    {
        return [
            [['company_id'], 'integer'],
            [['toDate', 'fromDate', 'bill_types', 'products'], 'safe'],
            [['fromDate'], 'default', 'value'=> date('Y-m-01')],
            [['toDate'], 'default', 'value'=> date('Y-m-t')],
            ['bill_types', 'each', 'rule' => ['integer']],
            ['products', 'each', 'rule' => ['integer']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'fromDate' => Yii::t('app', 'From Date'),
            'toDate' => Yii::t('app', 'To Date'),
            'products' => Yii::t('app', 'Products'),
            'bill_types' => Yii::t('app', 'Bill Types'),
            'company_id' => Yii::t('app', 'Company'),
        ];
    }

    /**
     * Retorna todos los productos facturados agrupados por producto y empresa
     *
     * @return Query
     */
    public function findIIBB()
    {
        $taxRate = TaxRate::findOne(['code'=>Config::getValue('default_tax_rate_code')]);

        /** @var Query $query */
        $query = (new Query());
        $query->select(['p.product_id', 'p.name as product', new Expression('sum(bd.qty) as qty'),
            new Expression('sum( if(bd.unit_net_discount = 0 or bd.unit_net_discount is null, (bd.line_total * bt.multiplier), -(bd.unit_net_discount'./*($taxRate->pct+1).*/') ) )  as total')])
            ->from('bill b')
            ->leftJoin('bill_detail bd', 'b.bill_id = bd.bill_id')
            ->leftJoin('product p', 'bd.product_id = p.product_id')
            ->leftJoin('bill_type bt', 'bt.bill_type_id = b.bill_type_id')
            // Se saca para que incluya todo.....
            // ->where('(b.ein IS NOT NULL AND b.ein <> \'\' OR ((b.ein IS NULL OR b.ein = \'\') AND bt.invoice_class_id IS NULL))')
            ->groupBy(['p.product_id', 'p.name'])
            ->orderBy(['p.type'=>SORT_DESC, 'p.name'=>SORT_ASC])
        ;

        if($this->company_id) {
            $query->andWhere(['b.company_id'=>$this->company_id]);
        }

        if(empty($this->fromDate)){
            $this->fromDate = (new \DateTime())->modify('first day of this month')->format('d-m-Y');
        }

        if(empty($this->toDate)){
            $this->toDate = (new \DateTime())->modify('last day of this month')->format('d-m-Y');
        }
        $query->andWhere(['<=', 'b.date', Yii::$app->formatter->asDate($this->toDate, 'yyyy-MM-dd')]);
        $query->andWhere(['>=', 'b.date', Yii::$app->formatter->asDate($this->fromDate, 'yyyy-MM-dd')]);

        if(!empty($this->bill_types)) {
            $query->andWhere(['in', 'b.bill_type_id',$this->bill_types]);
        }

        if(!empty($this->products)) {
            $products = $this->products;
            if(array_search(0, $products)!==false) {
                $products = array_merge($products,
                    ArrayHelper::map(Product::find()->where(['type'=>'plan'])->all(), 'product_id', 'product_id')
                    );
            }
            $query->andWhere(['in', 'p.product_id', $products]);
        }

        return $query;
    }

    public function findProducts()
    {
        $products[0] = Yii::t('app', 'Plans');
        $products = ArrayHelper::merge($products, ArrayHelper::map(Product::find()->where(['<>', 'type', 'plan'])->all(), 'product_id', 'name'));

        return $products;
    }
}