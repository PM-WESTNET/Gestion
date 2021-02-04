<?php

namespace app\modules\mobileapp\v1\models;

use app\modules\ticket\models\Category;
use app\modules\ticket\models\Schema;
use app\modules\ticket\models\Ticket;
use Yii;
use app\modules\sale\models\Customer;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "app_failed_register".
 *
 * @property integer $app_failed_register_id
 * @property string $name
 * @property string $lastname
 * @property string $document_type
 * @property string $document_number
 * @property integer $customer_code
 * @property string $email
 * @property string $email2
 * @property string $phone
 * @property string $phone2
 * @property string $phone3
 * @property string $phone4
 * @property string $status
 * @property integer $created_at
 */
class AppFailedRegister extends ActiveRecord
{

    const TYPE_REGISTER = 'register';
    const TYPE_CONTACT = 'contact';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'app_failed_register';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
                'createdAtAttribute' => 'created_at'
            ],
            /**'date' => [
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
            ],**/
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['status', 'text'], 'string'],
            [['customer_code'], 'integer'],
            [['document_type'], 'default', 'value' => null],
            [['document_number'], 'default', 'value' => null],
            [['email'], 'default', 'value' => null],
            [['name', 'document_type', 'document_number', 'phone', 'phone2', 'phone3', 'phone4'], 'string', 'max' => 45],
            [['email', 'email2'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'app_failed_register_id' => Yii::t('app', 'App Failed Register ID'),
            'name' => Yii::t('app', 'Name'),
            'document_type' => Yii::t('app', 'Document Type'),
            'document_number' => Yii::t('app', 'Document Number'),
            'email' => Yii::t('app', 'Email'),
            'phone' => Yii::t('app', 'Phone'),
            'status' => Yii::t('app', 'Status'),
            'fullName' => Yii::t('app', 'Name'),
            'type' => Yii::t('app', 'Type'),
            'text' => Yii::t('app','Text'),
            'customer_code' => Yii::t('app', 'Customer code'),
            'created_at' => Yii::t('app', 'Created At'),
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

    public function beforeSave($insert)
    {
        if ($insert){
            $this->status = 'pending';
        }

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    /**
     * @return string
     * Devuelve el nombre completo concatenando el apellido y el nombre.
     */
    public function getFullName(){
        return $this->name;
    }

    /**
     * @return array
     * Devuelve los tipos para ser listados en un desplegable
     */
    public static function getTypesForSelect()
    {
        return [
            self::TYPE_REGISTER => Yii::t('app', self::TYPE_REGISTER),
            self::TYPE_CONTACT => Yii::t('app', self::TYPE_CONTACT)
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub

        if ($insert) {

            $customer = Customer::findOne(['code' => $this->customer_code]);

            if ($customer) {
                $category = Category::findOne(['slug' => 'edicion-de-datos']);

                if ($category) {

                    $schema = Schema::findOne(['class' => 'app\modules\ticket\components\schemas\SchemaEdition']);

                    $status_id = ($schema ? $schema->statuses[0]->status_id : null);

                    $ticket= new Ticket();
                    $ticket->title = 'Solicitud de Edición de Datos';

                    $ticket->content = 'El cliente '. $customer->getFullName(). ' solicitó contacto para edición de Datos: <br>' .
                        (($this->name !== $customer->lastname. ' '. $customer->name) ? 'Nombre: ' . $this->name . "<br>" : '').
                        (($this->document_number !== $customer->document_number) ? 'Nro de Documento: ' . $this->document_number . "<br>" : '').
                        (($this->email !== $customer->email) ?'Email: ' . $this->email . "\n" : '').
                        (($this->email2 !== $customer->email2) ? 'Email Secundario: ' . $this->email2. "\n": '').
                        (($this->phone !== $customer->phone) ? 'Teléfono Fijo: ' . (!empty($this->phone) ? $this->phone : 'No definido'). "<br>" : '').
                        (($this->phone2 !== $customer->phone2) ? 'Celular 1: '. (!empty($this->phone2) ? $this->phone2 : 'No definido'). "<br>" : '').
                        (($this->phone3 !== $customer->phone3) ? 'Celular 2: '. (!empty($this->phone3) ? $this->phone3: 'No definido') . "<br>" : '').
                        (($this->phone4 !== $customer->phone4) ? 'Celular 3: '. (!empty($this->phone4) ? $this->phone4 : 'No definido') . "<br>" : '');
                    $ticket->customer_id = $customer->customer_id;
                    $ticket->category_id = $category->category_id;
                    $ticket->status_id = $status_id;
                    $ticket->user_id = 1;

                    if ($ticket->save() && Ticket::assignTicketToUser($ticket->ticket_id, $category->responsible_user_id)) {
                        //Ticket::assignTicketToUser($ticket->ticket_id, $category->responsible_user_id);
                        $this->updateAttributes(['ticket_id' => $ticket->ticket_id]);
                    }

                    Yii::info($ticket->getErrors());

                }
            }
        }
    }
}
