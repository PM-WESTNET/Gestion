<?php
namespace app\modules\backup\models;
use Yii;
use app\modules\config\models\Config;
use app\modules\westnet\notifications\components\helpers\LayoutHelper;
use app\modules\mailing\models\EmailTransport;

/**
 * @property integer $backup_id
 * @property integer $init_timestamp
 * @property integer $finish_timestamp
 * @property string $status
 * @property string $description
 * @property string $database
 */
class Backup extends \yii\db\ActiveRecord {


    public static function getDb()
    {
        return Yii::$app->get('dbbackups');
    }

    public static function tableName()
    {
        return 'backup';
    }

    public function rules()
    {
        return [
            [['init_timestamp'], 'required'],
            [['status'], 'string'],
            [['description', 'database', 'finish_timestamp'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
          'backup_id' => Yii::t('app','Backup'),
          'init_timestamp' => Yii::t('app','Begin'),
          'finish_timestamp' => Yii::t('app','End'),
          'status' => Yii::t('app', 'Status'),
          'description' =>   Yii::t('app', 'Description')
        ];
    }

    public function beforeSave($insert)
    {
        $this->formatDatesBeforeSave();

        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->formatDatesAfterFind();
    }

    public function getStatusLabel()
    {
        $statuses = [
            'in_process' => Yii::t('app','In Process'),
            'success' => Yii::t('app', 'Success'),
            'error' => Yii::t('app', 'Fail'),
        ];

        return $statuses[$this->status];
    }

    private function formatDatesBeforeSave()
    {
        if ($this->init_timestamp) {
            $this->init_timestamp = strtotime(Yii::$app->formatter->asDatetime($this->init_timestamp, 'yyyy-MM-dd HH:mm:ss'));
        }

        if ($this->finish_timestamp) {
            $this->finish_timestamp = strtotime(Yii::$app->formatter->asDatetime($this->finish_timestamp, 'yyyy-MM-dd HH:mm:ss'));
        }
    }

    private function formatDatesAfterFind()
    {
        if ($this->init_timestamp) {
            $this->init_timestamp = Yii::$app->formatter->asDatetime($this->init_timestamp, 'dd-MM-yyyy HH:mm:ss');
        }

        if ($this->finish_timestamp) {
            $this->finish_timestamp = Yii::$app->formatter->asDatetime($this->finish_timestamp, 'dd-MM-yyyy HH:mm:ss');
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($this->status === 'error') {
            $this->notifyError();
        }
    }

    private function notifyError()
    {
        $msg = <<<BODY
        <div class="backup-message">
        <h1>Ocurrió un error al realizar un backup</h1>
        <hr>
        
        <h4>Fecha de Inicio:  $this->init_timestamp </h4>
        <h4>Base de datos:  $this->database</h4>
        <h4>Descripción: $this->description</h4>
        
</div>   
BODY;
        $layout = LayoutHelper::getLayoutAlias('Info');
        Yii::$app->mail->htmlLayout = $layout;
        $emailTransport = EmailTransport::findOne(Config::getValue('defaultEmailTransport'));
        
        $mailSender = MailSender::getInstance(null, null, null, $emailTransport);
        $destinataries = explode(',', Config::getValue('backup_emails_notify'));

        $messages = [];
        foreach($destinataries as $destinatary) {
            $messages[] = $mailSender->prepareMessage(
                ['email'=>$destinatary, 'name' => $destinatary],
                'IMPORTANTE!!! - ERROR EN BACKUP DE GESTION',
                [ 'view'=> $msg ,'params' => []]
            );
        }

        $result = $mailSender->sendMultiple($messages);
    }




}


