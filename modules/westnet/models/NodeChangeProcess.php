<?php

namespace app\modules\westnet\models;

use app\components\db\ActiveRecord;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\westnet\components\ChangeNodeReader;
use Codeception\Util\Debug;
use webvimark\modules\UserManagement\models\User;
use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "node_change_process".
 *
 * @property int $node_change_process_id
 * @property string $created_at
 * @property string $ended_at
 * @property string $status
 * @property int $node_id
 * @property int $creator_user_id
 *
 * @property NodeChangeHistory[] $nodeChangeHistories
 * @property User $creatorUser
 * @property Node $node
 */
class NodeChangeProcess extends ActiveRecord
{
    const STATUS_CREATED  = 'created';
    const STATUS_PENDING  = 'pending';
    const STATUS_FINISHED = 'finished';
    const STATUS_REVERTED = 'reverted';

    public $file;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'node_change_process';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'node_id', 'creator_user_id'], 'required'],
            [['created_at', 'ended_at', 'file'], 'safe'],
            [['status'], 'string'],
            [['status'], 'in', 'range' => [self::STATUS_CREATED, self::STATUS_PENDING, self::STATUS_FINISHED]],
            [['node_id', 'creator_user_id'], 'integer'],
            [['creator_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_user_id' => 'id']],
            [['node_id'], 'exist', 'skipOnError' => true, 'targetClass' => Node::class, 'targetAttribute' => ['node_id' => 'node_id']],
            [['input_file'],'file']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'node_change_process_id' => Yii::t('app', 'Node Change Process'),
            'created_at' => Yii::t('app', 'Created At'),
            'ended_at' => Yii::t('app', 'Ended At'),
            'status' => Yii::t('app', 'Status'),
            'node_id' => Yii::t('app', 'Node ID'),
            'creator_user_id' => Yii::t('app', 'Creator User'),
            'file' => Yii::t('app', 'File'),
        ];
    }

    public function behaviors()
    {
        return [
            'created_at' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
                'value' => function(){
                    return (new \DateTime('now'))->format('Y-m-d H:i:s');
                }
            ],
        ];
    }

    /**
     * Valores de inicializacion
     */
    public function init()
    {
        parent::init();
        if(!$this->creator_user_id){
            $this->creator_user_id = Yii::$app->user->getId();
        }

        if(!$this->status) {
            $this->status = self::STATUS_CREATED;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNodeChangeHistories()
    {
        return $this->hasMany(NodeChangeHistory::class, ['node_change_process_id' => 'node_change_process_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatorUser()
    {
        return $this->hasOne(User::class, ['id' => 'creator_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNode()
    {
        return $this->hasOne(Node::class, ['node_id' => 'node_id']);
    }

    /**
     * Sube el archivo y le asigna el path correcto al modelo
     */
    public function upload()
    {
        $file = UploadedFile::getInstance($this, 'file');
        $folder = 'change-node';
        if ($file) {
            $filePath = Yii::$app->params['upload_directory'] . "$folder/". uniqid('file') . '.' . $file->extension;

            if (!file_exists(Yii::getAlias('@webroot') . '/' . Yii::$app->params['upload_directory'] . "$folder/")) {
                mkdir(Yii::getAlias('@webroot') . '/' . Yii::$app->params['upload_directory'] . "$folder/", 0775, true);
            }

            $file->saveAs(Yii::getAlias('@webroot') . '/' . $filePath);

            $this->input_file = $filePath;

            return true;
        } else {
            return false;
        }
    }

    /**
     * Indica si el modelo puede eliminarse
     */
    public function getDeletable()
    {
        if($this->status == self::STATUS_CREATED){
            return true;
        }

        return false;
    }

    /**
     * Indica si el archivo puede ser procesado
     */
    public function canBeProcessed()
    {
        if($this->status == self::STATUS_CREATED){
            return true;
        }

        return false;
    }

    /**
     * Devuelve un array con todos los estados posibles
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_CREATED => Yii::t('app', self::STATUS_CREATED),
            self::STATUS_PENDING => Yii::t('app', self::STATUS_PENDING),
            self::STATUS_FINISHED => Yii::t('app', self::STATUS_FINISHED),
            self::STATUS_REVERTED => Yii::t('app', self::STATUS_REVERTED),
        ];
    }

    /**
     * Cambia el estado del modelo, solo si no está en estado FINISHED
     */
    public function changeStatus($status)
    {
        if (array_key_exists($status, NodeChangeProcess::getStatuses())){
            if($this->status == self::STATUS_CREATED || $this->status == self::STATUS_PENDING){
                if($status == self::STATUS_FINISHED){
                    $this->updateAttributes(['status' => $status, 'ended_at' => (new \DateTime())->format('Y-m-d H:i:s')]);
                    return true;
                }

            }

            $this->updateAttributes(['status' => $status]);
            return true;
        }

        return false;
    }

    /**
     * Procesa el archivo, crea registros de NodeChangeHistory y realiza las actualizaciones de las conexiones.
     */
    public function processFile()
    {
        $errors = [];
        if($this->canBeProcessed()){
            $reader = new ChangeNodeReader();
            $all_data = $reader->parse($this);

            $this->changeStatus(self::STATUS_PENDING);
            foreach ($all_data as $data){
                $contract = Contract::findOne($data['contract_id']);
                if($contract){
                    $connection = $contract->connection;
                    if($connection){
                       $result = $this->changeNode($connection, $this->node_id);
                       if(!empty($result['errors'])){
                           foreach ($result['errors'] as $error) {
                               $errors[] = $error;
                           }
                       }
                    }
                }
            }
            $this->changeStatus(self::STATUS_FINISHED);
            return ['status' => true, 'errors' => $errors];
        } else {
            $errors[] = 'El archivo no puede ser procesado a menos que esté en estado creado';
        }

        return ['status' => false, 'errors' => $errors];
    }

    /**
     * Cambia el nodo de la connexion, actualiza la IP, y los datos de servidor.
     * Crea un registro histórico de actualización del nodo.
     */
    public function changeNode(Connection $connection, $new_node_id)
    {
        $errors = [];
        if ($connection->node_id == $new_node_id) {
            $errors[] = 'El nodo de destino es el nodo actual. Conexion '.$connection->connection_id;
            return ['status' => true, 'errors' => $errors];
        } else {
            try {
                $node = Node::findOne(['node_id' => $new_node_id]);
                $node_change_history = $this->fillChangeNodeHistory($connection);
                $connection->old_server_id = $connection->server_id;
                $connection->server_id = $node->server_id;
                $connection->node_id = $new_node_id;
                $connection->due_date = $connection->due_date ? $connection->due_date : null;
                $connection->updateIp();
                $connection->save();

                $node_change_history->new_ip = $connection->ip4_1;
                $node_change_history->save();
            } catch (\Exception $ex){
                $errors[] = 'Connection id: '.$connection->connection_id. '. '. $ex->getMessage();
            }
            return ['status' => true, 'errors' => $errors];
        }

        return [ 'status' => false, 'errors' => $errors ];
    }

    /**
     * Llena un modelo ChangeNodeHistory y lo devuelve
     */
    private function fillChangeNodeHistory(Connection $connection)
    {
        return new NodeChangeHistory([
            'old_node_id' => $connection->node_id,
            'connection_id' => $connection->connection_id,
            'old_ip' => $connection->ip4_1,
            'node_change_process_id' => $this->node_change_process_id,
            'status' => NodeChangeHistory::STATUS_APPLIED,
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Recorre cada uno de los registros de cambios realizados y ejecuta el rollback para cada uno
     */
    public function  rollback() {
        
        $errors= [];
        foreach($this->nodeChangeHistories as $history) {
            $r = $history->rollback();

            if($r['status'] === 'error') {
                $errors= array_merge($errors, $r['errors']);
            }

        }

        $this->changeStatus(self::STATUS_REVERTED);

        return [
            'status' => empty($errors) ? true : false,
            'errors' => $errors
        ];

    }
}
