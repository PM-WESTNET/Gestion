<?php

namespace app\modules\media\models;

use Yii;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "media".
 *
 * @property integer $media_id
 * @property string $title
 * @property string $description
 * @property string $name
 * @property string $base_url
 * @property string $relative_url
 * @property string $mime
 * @property double $size
 * @property integer $width
 * @property integer $height
 * @property string $extension
 * @property string $create_date
 * @property string $create_time
 * @property integer $create_timestamp
 * @property string $status
 *
 * @property Data[] $datas
 * @property ModelHasMedia[] $modelHasMedia
 */
class Media extends \yii\db\ActiveRecord
{
 
    public $file;

    public static function instantiate($row)
    {
        
        $class = 'app\modules\media\models\types\\'.$row['type'];
        
        if( class_exists($class) ){
            return new $class;
        }
        
        return new self;
        
    }
    
    public function init()
    {
        $this->status = 'enabled';
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'media';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dbmedia');
    }

    public function behaviors()
    {
        return [
            'datestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['create_date'],
                ],
                'value' => function(){return date('Y-m-d');},
            ],
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['create_time'],
                ],
                'value' => function(){return date('H:i');},
            ],
            'unix_timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['create_timestamp'],
                ],
            ],
        ];
    }
    
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'in', 'range'=>['enabled','disabled']],
            [['title'], 'string', 'max' => 140],
            [['description'], 'string', 'max' => 255],
            [['file'], 'file', 'skipOnEmpty' => false],
        ];
    }
    
    /**
     * Devuelve los parametros configurados para este tipo de media
     * @return array
     */
    protected function getParams(){
        
        $params = Yii::$app->getModule('media')->params;
        
        $c = get_called_class();
        $c = new \ReflectionClass($c);
        $c = $c->getShortName();
        
        if($c && isset($params[$c])){
            return $params[$c];
        }
        
        return [];
        
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'media_id' => Yii::t('app', 'Media ID'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'name' => Yii::t('app', 'Name'),
            'base_url' => Yii::t('app', 'Base Url'),
            'relative_url' => Yii::t('app', 'Relative Url'),
            'url' => Yii::t('app', 'Url'),
            'class' => Yii::t('app', 'Class'),
            'mime' => Yii::t('app', 'Mime'),
            'size' => Yii::t('app', 'Size'),
            'width' => Yii::t('app', 'Width'),
            'height' => Yii::t('app', 'Height'),
            'extension' => Yii::t('app', 'Extension'),
            'create_date' => Yii::t('app', 'Create Date'),
            'create_time' => Yii::t('app', 'Create Time'),
            'create_timestamp' => Yii::t('app', 'Create Timestamp'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getData()
    {
        return $this->hasMany(Data::className(), ['media_id' => 'media_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModels()
    {
        return $this->hasMany(ModelHasMedia::className(), ['media_id' => 'media_id']);
    }
    
    public function getUrl(){
        
        return $this->base_url . $this->relative_url;
        
    }
    
    public function upload()
    {
        $this->file = UploadedFile::getInstanceByName('Media[file]');

        if ($this->validate()) {
            
            $directory = 'uploads/'.date('Y').'/'.date('m').'/';
            
            FileHelper::createDirectory($directory, 0775, true);
            $path = $directory . $this->file->baseName . uniqid() . '.' . $this->file->extension;
            
            $this->file->saveAs($path, false);
            $this->relative_url = $path;
            $this->extension = $this->file->extension;
            $this->size = $this->file->size;
            $this->mime = FileHelper::getMimeType($path);
            
            $this->proccess($path);
            
            return true;
        } else {
            return false;
        }
    }
    
    protected function proccess($file)
    {
    }
    
    public function beforeSave($insert) 
    {
        $this->base_url = Yii::getAlias('@web');
        
        return parent::beforeSave($insert);
    }
    
    /**
     * Ancho y alto definidos en params['sizes']
     * @param string $size
     * @return array
     */
    public function getSize($size)
    {
        if(isset($size['width']) && isset($size['height'])){
            return [$size['width'], $size['height']];
        }
        
        $params = Yii::$app->getModule('media')->params;
        if(isset($params['sizes'][$size])){
            return $params['sizes'][$size];
        }
            
        return $params['sizes']['default'];

    }
    
    public function fields() {
        $fields = parent::fields();
        
        return array_merge($fields, [
            'thumbnail' => function($model){
                return $model->getSizedUrl(300, 300);
            }
        ]);
    }
}
