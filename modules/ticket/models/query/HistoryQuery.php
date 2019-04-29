<?php

namespace app\modules\ticket\models\query;

use \app\modules\ticket\models\History;

/**
 * This is the ActiveQuery class for [[\app\modules\ticket\models\Ticket]].
 *
 * @see \app\modules\ticket\models\Ticket
 */
class HistoryQuery extends \yii\db\ActiveQuery {

    /**
     * Adds condition where title is "reopened"
     * @return type
     */
    public function isLastReopened() {
        return $this->andWhere(['title' => History::TITLE_REOPENED])->orderBy([
                    'datetime' => SORT_DESC
        ]);
    }

    /**
     * Adds condition where title is "closed"
     * @return type
     */
    public function isLastClosed() {
        return $this->andWhere(['title' => History::TITLE_CLOSED])->orderBy([
                    'datetime' => SORT_DESC
        ]);
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
