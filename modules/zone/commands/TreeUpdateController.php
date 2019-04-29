<?php

namespace app\modules\zone\commands;

use app\modules\zone\models\Zone;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Class ZoneController
 * Se actualizan todas las zonas desde los nodos padres.
 * @package app\commands
 */
class TreeUpdateController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex()
    {
        //Intro
        $this->stdout('Arya[Zone]', Console::BOLD, Console::FG_CYAN);
        $this->stdout(" | Command to update the zone tree \n\n", Console::BOLD);
        echo "Starting... \n";

        $this->updateTree(0);
        $this->stdout("\nProccess successfully finished. \n", Console::BOLD, Console::FG_GREEN);
    }

    /**
     * Actualiza el arbol de zonas.
     *
     * @param int $parent_id
     * @param int $left
     * @return int
     * @internal param int|null $parent_account_id
     */
    private function updateTree($parent_id=0, $left = 0)
    {
        $right = $left +1;
        $query = Zone::find()
            ->where(['parent_id'=>$parent_id]);
        if($parent_id==0) {
            $query->orWhere(['parent_id'=>null]);
        }
        $zones = $query->all();

        foreach($zones as $zone) {
            $right = $this->updateTree($zone->zone_id, $right);
        }
        Yii::$app->db->createCommand('update zone set lft=' . $left . ", rgt=".$right . " WHERE zone_id=".$parent_id)->execute();

        return $right +1;
    }
}