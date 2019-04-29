<?php

namespace app\modules\backup\models;

use yii\base\Model;

/**
 * Backup
 *
 * Yii module to backup, restore databse
 *
 * @version 1.0
 * @author Shiv Charan Panjeta <shiv@toxsl.com> <shivcharan.panjeta@gmail.com>
 */
/**
 * UploadForm class.
 * UploadForm is the data structure for keeping
 */
class BackupFile extends Model
{
    public $id ;
    public $name ;
    public $size ;
    public $create_time ;
    public $modified_time ;
    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules()
    {
        return array(
            array(['id','name','size','create_time','modified_time'], 'required'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'name' => Yii::t('backup','File name'),
            'size' => Yii::t('backup','File size'),
            'create_time'=> Yii::t('backup','Create time'),
            'modified_time'=> Yii::t('backup','Modified time'),
        );
    }
    
    public static function label($n = 1) {
            return Yii::t('app', 'Backup File|Backup Files', $n);
    }
}
