<?php
namespace app\modules\backup\models;
use Yii;

/**
 * @property integer $backup_id
 * @property integer $init_timestamp
 * @property integer $finish_timestamp
 * @property string $status
 * @property string $description
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
            [['init_timestamp', 'finish_timestamp'], 'integer'],
            [['status'], 'string']
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

    private function notifyError(){

    }


}


