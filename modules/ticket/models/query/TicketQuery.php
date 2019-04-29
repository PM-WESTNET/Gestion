<?php

namespace app\modules\ticket\models\query;

use app\modules\ticket\models\Status;

/**
 * This is the ActiveQuery class for [[\app\modules\ticket\models\Ticket]].
 *
 * @see \app\modules\ticket\models\Ticket
 */
class TicketQuery extends \yii\db\ActiveQuery {

    /**
     * Adds condition where status_id = StatusOpen.status_id
     * @return type
     */
    public function isStatusOpen() {
        
        $statusActive = Status::find()->where([
            'is_open' => 1
        ])->all();
        
        return $this->andWhere(['status_id' => \yii\helpers\ArrayHelper::getColumn($statusActive, 'status_id')]);
    }

    /**
     * @inheritdoc
     * @return \app\modules\ticket\models\Ticket[]|array
     */
    public function all($db = null) {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\modules\ticket\models\Ticket|array|null
     */
    public function one($db = null) {
        return parent::one($db);
    }

}
