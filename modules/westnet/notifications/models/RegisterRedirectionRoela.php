<?php

namespace app\modules\westnet\notifications\models;
use Yii;

/**
 * This is the model class for table "register_redirection_roela".
 *
 * @property integer $register_redirection_roela_id
 * @property string $resultado_id
 * @property string $referencia_operacion
 * @property string $description
 * @property string $created_at
 */

class RegisterRedirectionRoela extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'register_redirection_roela';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'resultado_id', 'referencia_operacion', 'description'], 'safe'],
            [['register_redirection_roela_id'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'register_redirection_roela_id' => 'Registro Redireccion Roela ID',
            'resultado_id' => 'Resultado ID',
            'referencia_operacion' => 'Referencia Operacion',
            'description' => 'Descripcion',
            'created_at' => 'Fecha de Creacion'
        ];
    }    

     
    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        return true;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: None.
     */
    protected function unlinkWeakRelations(){
    }
    
    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if($this->getDeletable()){
                $this->unlinkWeakRelations();
                return true;
            }
        } else {
            return false;
        }
    }

}
