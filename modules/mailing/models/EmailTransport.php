<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 28/08/17
 * Time: 10:34
 */

namespace app\modules\mailing\models;

use app\components\db\ActiveRecord;
use app\modules\mailing\MailingModule;
use Yii;

/**
 * This is the model class for table "email_transport".
 *
 * @property integer $email_transport_id
 * @property string $name
 * @property string $from_email
 * @property string $transport
 * @property string $host
 * @property integer $port
 * @property string $username
 * @property string $password
 * @property string $encryption
 * @property string $layout
 * @property string $relation_class
 * @property integer $relation_id
 */
class EmailTransport extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'email_transport';
    }

    /**
     * @inheritdoc
     */
    /*
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['timestamp'],
                ],
            ],
            'date' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date'],
                ],
                'value' => function(){return date('Y-m-d');},
            ],
            'time' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['time'],
                ],
                'value' => function(){return date('h:i');},
            ],
        ];
    }
    */

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'from_email', 'transport', 'host', 'port', 'username', 'password'], 'required'],
            [['port', 'relation_id'], 'integer'],
            [['name', 'from_email', 'host', 'username', 'password'], 'string', 'max' => 50],
            [['transport', 'layout', 'relation_class'], 'string', 'max' => 100],
            [['encryption'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email_transport_id' => MailingModule::t('Email Transport ID'),
            'name' => MailingModule::t('Name'),
            'from_email' => MailingModule::t('From Email'),
            'transport' => MailingModule::t('Transport'),
            'host' => MailingModule::t('Host'),
            'port' => MailingModule::t('Port'),
            'username' => MailingModule::t('Username'),
            'password' => MailingModule::t('Password'),
            'encryption' => MailingModule::t('Encryption'),
            'layout' => MailingModule::t('Layout'),
            'relation_class' => MailingModule::t('Relation Class'),
            'relation_id' => MailingModule::t('Relation ID'),
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

    /**
     * Retorno un array con la configuracion del transport
     * @return array
     */
    public function getConfigArray()
    {
        return [
            'class'         => $this->transport,
            'host'          => $this->host,
            'username'      => $this->username,
            'password'      => $this->password,
            'port'          => $this->port,
            'encryption'    => $this->encryption
        ];
    }

    public function getText()
    {
        if($this->relation_id) {
            $oTrans = new $this->relation_class;
            Yii::debug(get_class($oTrans));
            $oRs = $oTrans->findForAutoComplete('');
            $result = [];
            foreach($oRs as $key => $data) {
                if($key == $this->relation_id) {
                    return $data;
                }
            }
        }
        return "";
    }

    public function getObject()
    {
        if($this->relation_id) {
            $class = $this->relation_class;
            $pkField = $class::primaryKey();
            return $class::findOne([ $pkField[0]  => $this->relation_id]);
        }
        return null;
    }
}