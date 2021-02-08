<?php

namespace app\modules\ticket\components\schemas;

use app\modules\ticket\models\Schema;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "schema".
 *
 * @property int $schema_id
 * @property string $name
 */
class SchemaInstalaciones extends Schema implements SchemaInterface
{
    /**
     * @param null $category_id
     * Devuelve todos los estados
     */
    public static function getSchemaStatuses()
    {
        $schema = self::findOne(['class' => self::class]);
        if($schema) {
            return $schema->statuses;
        }
    }

    /**
     * @return array
     * Devuelve los estados asociados a este schema para ser listados en un select2
     */
    public static function getStatusesForSelect()
    {
        $schema = self::findOne(['class' => self::class]);
        if($schema) {
            return ArrayHelper::map($schema->statuses, 'status_id', 'name');
        }
    }
}
