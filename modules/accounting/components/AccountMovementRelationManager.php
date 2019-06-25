<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 23/08/16
 * Time: 13:39
 */

namespace app\modules\accounting\components;


use app\modules\accounting\models\AccountMovement;
use app\modules\accounting\models\AccountMovementRelation;
use Yii;
use yii\web\Application;

/**
 * Class AccountMovementRelationManager
 * Manager para las relaciones entre movimientos y otras entidades.
 *
 * @package app\modules\accounting\components
 */
class AccountMovementRelationManager
{
    /**
     * Borro la relacion del movimiento dado un objeto.
     *
     * @param $obj Objeto
     */
    public static function delete($obj)
    {
        try{
            $amr = AccountMovementRelationManager::find($obj);

            if ( $amr->accountMovement->status == AccountMovement::STATE_DRAFT ) {
                $amr->delete();

                return $amr->accountMovement->delete();
            }
        } catch(\Exception $ex) {
            if(Yii::$app instanceof Application) {
                Yii::$app->session->addFlash('error', Yii::t('accounting',  'The Account Movement Relation can\'t be deleted.'));
            } else {
                echo  Yii::t('accounting',  'The Account Movement Relation can\'t be deleted.');
            }
        }

        return false;
    }

    /**
     * Dado un movimiento y un objeto, guardo la relacion.
     *
     * @param $movement_id
     * @param $obj
     */
    public static function save($movement_id, $obj)
    {
        try{
            $amr = new AccountMovementRelation();
            $amr->account_movement_id = $movement_id;
            $amr->class = get_class($obj);
            $pk_field = $obj->tableSchema->primaryKey[0];
            $amr->model_id = $obj->$pk_field;
            $amr->save();
        } catch(\Exception $ex){
            if(Yii::$app instanceof Application) {
                Yii::$app->session->addFlash('error', Yii::t('accounting', 'The Account Movement Relation can\'t be saved.'));
            } else {
                echo  Yii::t('accounting', 'The Account Movement Relation can\'t be saved.');
            }
        }
    }


    public static function find($obj)
    {
        try{
            $class = get_class($obj);
            $pk = $obj->tableSchema->primaryKey[0];

            $amr = AccountMovementRelation::findOne(['class'=> $class, 'model_id' => $obj->$pk ]);

            return $amr;
        } catch(\Exception $ex) {
            if(Yii::$app instanceof Application) {
                Yii::$app->session->addFlash('error', Yii::t('accounting', 'The Account Movement Relation can\'t be finded.'));
            } else {
                echo Yii::t('accounting', 'The Account Movement Relation can\'t be finded.');
            }
        }

        return false;
    }

    /**
     * @param $obj
     * @return bool
     * Indica si el objeto se puede eliminar bajo los siguientes criterios:
     *  - Que el movimiento que esta relacionado con el objeto no este en estado cerrado
     */
    public static function isDeletable($obj)
    {
        $relation = AccountMovementRelationManager::find($obj);

        if($relation) {
            if($relation->accountMovement->status == AccountMovement::STATE_CLOSED) {
                return false;
            } else {
                return true;
            }
        }

        return true;
    }
}