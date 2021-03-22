<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 26/05/17
 * Time: 14:12
 */

namespace app\modules\westnet\components;


use app\components\helpers\EmptyLogger;
use app\modules\westnet\isp\Profiler;
use app\modules\westnet\models\Connection;
use app\modules\westnet\models\Node;
use yii\db\Query;

class ConnectionService
{
    public function changeServer($node_id, $new_server_id)
    {

        //\Yii::setLogger(new EmptyLogger());
        // Actualizo el nodo

        $node = Node::findOne(['node_id'=>$node_id]);
        $old_server_id = $node->server_id;
        $node->server_id = $new_server_id;
        $node->save();

        /** @var Query $query */
        $query = Connection::find()->where('node_id = '. $node_id);

        $qty = 1;
        $total = $query->count();

        \Yii::$app->params['apply_wispro'] = false;
        error_log( "empieza: " . (new \DateTime('now'))->format('Y-m-d H:i:s'));
        Profiler::profile('change-server');
        try {
            /** @var Connection $connection */
            foreach($query->all() as $connection) {
                $connection->old_server_id = $connection->server_id;
                $connection->server_id = $new_server_id;
                $connection->clean = true;
                $connection->save();


                \Yii::$app->session->close();
                \Yii::$app->session->set( '_change_node_', [
                    'qty' => $qty,
                    'total' => $total
                ]);
                $qty++;
            }
        } catch (\Exception $ex) {
            error_log(get_class($this). " - changeServer: ". $ex->getMessage() . " - " . $ex->getFile() . " - " . $ex->getLine() );
            $node->server_id = $old_server_id;
            $node->save();
        }

        Profiler::profile('change-server');
        Profiler::printTimes(true);
        error_log("termina: ".(new \DateTime('now'))->format('Y-m-d H:i:s'));

    }
}