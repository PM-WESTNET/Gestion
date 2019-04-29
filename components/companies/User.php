<?php

namespace app\components\companies;

use Yii;
use app\modules\sale\models\Company;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property integer $email_confirmed
 * @property string $auth_key
 * @property string $password_hash
 * @property string $confirmation_token
 * @property string $bind_to_ip
 * @property string $registration_ip
 * @property integer $status
 * @property integer $superadmin
 * @property integer $created_at
 * @property integer $updated_at
 */
class User extends \webvimark\modules\UserManagement\components\UserConfig
{
    private $_companies;
    
	/**
	 * @return array
	 */
	public function attributeLabels()
	{
        //Si es necesario, pisar estas traducciones:
//		return [
//			'id'                 => 'ID',
//			'username'           => UserManagementModule::t('back', 'Login'),
//			'superadmin'         => UserManagementModule::t('back', 'Superadmin'),
//			'confirmation_token' => 'Confirmation Token',
//			'registration_ip'    => UserManagementModule::t('back', 'Registration IP'),
//			'bind_to_ip'         => UserManagementModule::t('back', 'Bind to IP'),
//			'status'             => UserManagementModule::t('back', 'Status'),
//			'gridRoleSearch'     => UserManagementModule::t('back', 'Roles'),
//			'created_at'         => UserManagementModule::t('back', 'Created'),
//			'updated_at'         => UserManagementModule::t('back', 'Updated'),
//			'password'           => UserManagementModule::t('back', 'Password'),
//			'repeat_password'    => UserManagementModule::t('back', 'Repeat password'),
//			'email_confirmed'    => UserManagementModule::t('back', 'E-mail confirmed'),
//			'email'              => 'E-mail',
//		];
        
        return array_merge(parent::attributeLabels(), [
            'companies' => Yii::t('app', 'Companies')
        ]);
	}
    
    public function getCompanies(){
        return $this->hasMany(Company::className(), ['user_id' => 'user_id'])->viaTable('user_has_company', ['company_id' => 'company_id']);
    }
    
    public function setCompanies($companies){
        if(empty($companies)){
            $companies = [];
        }
        
        $this->_companies = $companies;

        $saveCompanies = function($event){
            //Quitamos las relaciones actuales
            $this->unlinkAll('companies', true);
            //Guardamos las nuevas relaciones
            foreach ($this->_companies as $id){
                $this->link('companies', Company::findOne($id));
            }
        };

        $this->on(self::EVENT_AFTER_INSERT, $saveCompanies);
        $this->on(self::EVENT_AFTER_UPDATE, $saveCompanies);
    }
    
}
