<?php
/**
 */

namespace app\components\companies;

use Yii;
use yii\db;

/**
 * Si la sesion actual tiene establecida la variable company_id, agrega una condicion
 * a cualquier busqueda la condicion AND company_id=company_id.
 */
class ActiveRecord extends \app\modules\log\db\ActiveRecord
{
    
    protected static $companyRequired = true;
    
    public $companyIdAttribute = 'company_id';

    /**
     * @return ActiveQuery the newly created [[ActiveQuery]] instance.
     */
    public static function find()
    {   
        //El query inicial es identico al implementado en la clase padre
        $query = Yii::createObject(db\ActiveQuery::className(), [get_called_class()]);
        
        return $query;
    }
    
    public function beforeSave($insert) {
        
        if(parent::beforeSave($insert)){
            $company_id=$this->getCompanyId();
            if(empty($company_id) && static::$companyRequired){
                $this->addError($this->companyIdAttribute, Yii::t('app','Company required.'));
                return false;
            }
        
            return true;
        }
        
        return false;
    }
    
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(\app\modules\sale\models\Company::className(), [$this->companyIdAttribute => 'company_id']);
    }
    
    /**
     * Devuelve el id de la empresa
     * @return int
     */
    public function getCompanyId(){
        
        $attr = $this->companyIdAttribute;
        return $this->$attr;
        
    }

    /**
     * Setea el id de la empresa
     * @return int
     */
    public function setCompanyId($value){

        $attr = $this->companyIdAttribute;
        $this->$attr = $value;
        return $this;
    }
}
