<?php

namespace app\components\user;

use app\modules\sale\models\Company;

/**
 * Class User
 * @package app\components\user
 * Aquellos modelos que necesiten guardar la relacion en user_has_company deberian extender de este modelo
 */
class User extends \webvimark\modules\UserManagement\models\User
{
    const SCENARIO_CREATE = 'newUser';

    public $_companies;

    public function rules()
    {
        return [
            ['username', 'unique'],
            ['username', 'trim'],
            [['status'], 'integer'],
            ['email', 'email'],
            ['bind_to_ip', 'trim'],
            ['bind_to_ip', 'string', 'max' => 12],
            [['companies'], 'safe'],
            ['username', 'required', 'on'=>[self::SCENARIO_CREATE, 'changePassword']],
            ['password', 'required', 'on'=>[self::SCENARIO_CREATE, 'changePassword']],
            ['repeat_password', 'required', 'on'=>[self::SCENARIO_CREATE, 'changePassword']],
            ['repeat_password', 'compare', 'compareAttribute'=>'password'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'companies' => \Yii::t('app', 'Access to companies')
        ];
    }

    public function getCompanies(){
        return $this->hasMany(Company::class, ['company_id' => 'company_id'])->viaTable('user_has_company', ['user_id' => 'id']);
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
