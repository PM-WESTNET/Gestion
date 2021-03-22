<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 6/06/16
 * Time: 11:12
 */

namespace app\components\db;


use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\db\QueryInterface;

/**
 * Class BigDataProvider
 * Clase para el uso de SQL_CALC_FOUND_ROWS y FOUND_ROWS como DataProvider.
 * Para poder usar hay que tener un par de consideraciones:
 *  - En el select principal usar ->select([new Expression('SQL_CALC_FOUND_ROWS *')])
 *  - El paginado no funciona por lo que se puso
 *              'pagination' => [
 *                  'pageSize' => 20,
 *                  'page' => (isset($params['page']) ? $params['page'] -1 : 0 )
 *              ],
 * @package app\components\db
 */
class BigDataProvider extends ActiveDataProvider
{
    public function prepareTotalCount()
    {
        $queryCount = (new Query())
            ->select(new Expression('FOUND_ROWS() as count'))
            ->one();
        return $queryCount['count'];
    }

    /**
     * @inheritdoc
     */
    protected function prepareModels()
    {
        if (!$this->query instanceof QueryInterface) {
            throw new InvalidConfigException('The "query" property must be an instance of a class that implements the QueryInterface e.g. yii\db\Query or its subclasses.');
        }

        $query = clone $this->query;
        if (($pagination = $this->getPagination()) !== false) {
            $query->limit($pagination->getLimit())->offset($pagination->getOffset());
        }
        if (($sort = $this->getSort()) !== false) {
            $query->addOrderBy($sort->getOrders());
        }

        $all = $query->all($this->db);
        $pagination->totalCount = $this->getTotalCount();

        return $all;
    }
}
