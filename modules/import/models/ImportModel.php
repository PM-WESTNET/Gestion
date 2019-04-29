<?php

namespace app\modules\import\models;

use \Yii;

/**
 * Description of FileModel
 *
 * @author martin
 */
class ImportModel extends \yii\base\Model
{
    
    public $model;
    public $file;
    
    public function rules(){
        
        return [
            [['file'],'file'],
            [['model'],'required']
        ];
        
    }
    
    public function attributeLabels() {
        return [
            'file' => Yii::t('import','File'),
            'model' => Yii::t('import','Model'),
        ];
    }
    
}
