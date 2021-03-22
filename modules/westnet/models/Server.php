<?php

namespace app\modules\westnet\models;

use app\modules\westnet\isp\ServerInterface;
use app\modules\westnet\models\search\ConnectionSearch;
use Yii;

/**
 * This is the model class for table "server".
 *
 * @property integer $server_id
 * @property string $name
 * @property string $status
 * @property string $url
 * @property string $token
 * @property string $user
 * @property string $password
 * @property string $class
 *
 * @property Node[] $nodes
 */
class Server extends \app\components\db\ActiveRecord implements ServerInterface
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'server';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'url'], 'required'],
            [['status'], 'string'],
            [['name'], 'string', 'max' => 45],
            [['url', 'token', 'user', 'password', 'class'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'server_id' => Yii::t('westnet', 'Server'),
            'name' => Yii::t('app', 'Name'),
            'status' => Yii::t('app', 'Status'),
            'nodes' => Yii::t('westnet', 'Nodes'),
            'url' => Yii::t('westnet', 'Url'),
            'token' => Yii::t('westnet', 'Token'),
            'user' => Yii::t('westnet', 'User'),
            'password' => Yii::t('westnet', 'Password'),
            'class' => Yii::t('westnet', 'Clase'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNodes()
    {
        return $this->hasMany(Node::className(), ['server_id' => 'server_id']);
    }
    
        
             
    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        return ($this->getNodes()->count() ==0);
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: Nodes.
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

    public function moveCustomersTo($server_id)
    {
        $errors = [];
        $search = new ConnectionSearch();
        $connections = $search->findByServer($this->server_id)->all();;
        $i = 0;
        /** @var Connection $connection */
        foreach($connections as $connection) {
            $connection->server_id = $server_id;
            $connection->force_update_isp = true;
            if(!$connection->updateAttributes(['server_id'=>$server_id])) {
                $errors[] = $connection->contract->customer->name;
            }
            if(($i%10)==0) {
                Yii::$app->session->close();
            }
            $i++;
        }
        return $errors;
    }

    public function restoreCustomersFromNode()
    {
        $errors = [];
        $search = new ConnectionSearch();
        $connections = $search->findByServer($this->server_id)->all();;
        foreach($connections as $key=>$connection) {
            $connection->server_id = $connection->node->server_id;
            if(!$connection->updateAttributes(['server_id'=>$connection->node->server_id])) {
                $errors[] = $connection->contract->customer->name;
            }
        }
        return $errors;
    }

    /**
     * Retorna la URL de conexion al servidor.
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Retorna el usuario con el que se conecta al servidor
     *
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Retorna la contraseña con el que se conecta al servidor.
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Retorna el token con el que se conecta al servidor
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Retorna la clase que implementa el isp
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Setea el token con el que se conecta al servidor
     * @return mixed
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }
}
