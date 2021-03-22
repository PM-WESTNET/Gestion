<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 8/11/16
 * Time: 13:15
 */

namespace app\modules\westnet\components;

use app\modules\checkout\models\Payment;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class ReferencedDiscountBehavior extends Behavior
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

    }

    /**
     * Eventos que dispara el Behavior
     *
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_UPDATE    => 'afterUpdate',
        ];
    }

    public function afterUpdate($event)
    {
        if($event->sender instanceof Payment) {
            if ( $event->sender->status == Payment::PAYMENT_CLOSED ) {
                $rd = new ReferencedDiscount();
                try {
                    $rd->applyDiscount($event->sender);
                } catch( \Exception $ex) {
                    if(Yii::$app->session) {
                        Yii::$app->session->addFlash('error', $ex->getMessage());
                    }
                }
            }
        }
    }
}