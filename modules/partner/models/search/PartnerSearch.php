<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 12/05/16
 * Time: 12:07
 */

namespace app\modules\partner\models\search;

use app\modules\config\models\Config;
use app\modules\partner\models\Partner;
use app\modules\partner\models\PartnerDistributionModelHasPartner;
use yii\db\Expression;
use yii\db\Query;

class PartnerSearch extends Partner
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['partner_id'], 'integer'],
        ];
    }

    /**
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchStatus($partner_id)
    {
        $query = new Query();
        $query->select(['c.name', 'sum(pl.debit) as debit', 'sum(pl.credit) as credit', new Expression('sum(pl.credit) - sum(pl.debit) as balance')])
            ->from('company c')
            ->innerJoin('partner_distribution_model pdm', 'c.company_id = pdm.company_id')
            ->innerJoin('partner_distribution_model_has_partner pdmhp', 'pdm.partner_distribution_model_id = pdmhp.partner_distribution_model_id')
            ->innerJoin('partner p', 'pdmhp.partner_id = p.partner_id')
            ->leftJoin('partner_liquidation pl', 'pdmhp.partner_distribution_model_has_partner_id = pl.partner_distribution_model_has_partner_id')
            ->andWhere(['p.partner_id'=> $partner_id])
            ->groupBy(['c.name'])
            ->having(['>', new Expression('(sum(pl.debit) + sum(pl.credit))'), 0])
        ;

        return $query;
    }

}
