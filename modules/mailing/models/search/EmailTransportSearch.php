<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 28/08/17
 * Time: 11:25
 */

namespace app\modules\mailing\models\search;


use app\modules\mailing\models\EmailTransport;

class EmailTransportSearch extends EmailTransport
{

    public function findBy($name=null, $relation_class=null, $relation_id=null)
    {
        $query = self::find();
        $query->filterWhere([
            'name'              => $name,
            'relation_class'    => $relation_class,
            'relation_id'       => $relation_id
        ]);

        return $query->one();
    }
}
