<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 15/01/18
 * Time: 11:04
 */

namespace app\components\db;


use Yii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;
use yii\web\Application;

class ModifierBehavior extends Behavior
{
    public $created_at;
    public $updated_at;
    public $creator_user_id;
    public $updater_user_id;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
        ];
    }

    public function beforeInsert($event)
    {
        $object = $event->sender;
        $object->updated_at = null;
        $object->updater_user_id = null;
        $object->created_at = (new \DateTime('now'))->getTimestamp();
        if (Yii::$app instanceof Application) {
            $object->creator_user_id = Yii::$app->user->id;
        }
    }

    public function beforeUpdate($event) {
        $object = $event->sender;
        $object->updated_at = (new \DateTime('now'))->getTimestamp();;
        if(Yii::$app instanceof Application) {
            $object->updater_user_id = Yii::$app->user->id;
        }
    }
}