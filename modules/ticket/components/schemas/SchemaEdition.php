<?php

namespace app\modules\ticket\components\schemas;

use app\modules\ticket\models\Schema;
use Yii;
use yii\helpers\ArrayHelper;

class SchemaEdition extends Schema implements SchemaInterface
{
    /**
     * @param null $category_id
     * Devuelve todos los estados
     */
    public static function getSchemaStatuses()
    {
        $schema = Schema::findOne(['class' => self::class]);

        if($schema) {
            return $schema->statuses;
        }
    }

    /**
     * @return array
     * Devuelve los estados asociados a este schema para ser listados en un select2
     */
    public function getStatusForSelect()
    {
        return ArrayHelper::map($this->getStatuses(), 'status_id', 'name');
    }
}
