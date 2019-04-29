<?php

namespace app\modules\media\models\types;

use Yii;
use app\modules\media\models\Type;
use app\modules\media\models\Media;
use app\modules\media\models\Sized;
use yii\helpers\FileHelper;
use app\modules\config\models\Config;

/**
 * Description of Image
 *
 * @author mmoyano
 */
class Image extends Media{
    
    public $file;
    
    public function init()
    {
        parent::init();
        $this->type = 'Image';
    }
    
    public function rules()
    {
        
        $params = $this->params;
        
        $rules = parent::rules();
        $rules[] = ['file', 'image', 
            'extensions' => $params['extensions'],
            'minWidth' => Config::getValue('image_min_width'), //$params['minWidth'], 
            'maxWidth' => Config::getValue('image_max_width'), //$params['maxWidth'],
            'minHeight' => Config::getValue('image_min_height'), //$params['minHeight'], 
            'maxHeight' => Config::getValue('image_max_height'), //$params['maxHeight'],
        ];
        
        return $rules;
    }

    public static function find()
    {
        return new Type(get_called_class(), ['type' => self::$type]);
    }

    public function getUrl()
    {
        return $this->base_url.'/'.$this->relative_url;
    }
    
    public function render($width = null, $height = null, $options = [])
    {
        if($width === null && $height === null){
            return \yii\helpers\Html::img($this->url, $options);
        }
        return \yii\helpers\Html::img($this->getSizedUrl($width, $height), $options);
    }
    
    protected function proccess($path)
    {
        
        $size = getimagesize($path);
        $this->width = $size[0];
        $this->height = $size[1];
        
    }
    
    public function getSizedUrl($width, $height)
    {
        
        if(($this->width == $width && $this->height == $height) 
                || ($this->width == $width && $this->height == null) 
                || ($this->width == null && $this->height == $height)){
            
            return $this->url;
        }
        
        //Si el ancho o el alto de la imagen es menor al requerido, utilizamos el valor actual
        //De no hacerlo, nunca se encontrara la version del tamanio solicitado
        if($this->width < $width){
            $width = $this->width;
        }
        if($this->height < $height){
            $height = $this->height;
        }
        
        //Ignoramos el ancho o el alto si son nulls (con andFilterWhere)
        $sized = $this->getSizeds()->andFilterWhere(['width' => $width, 'height' => $height])->one();
        if($sized){
            return $sized->url;
        }
        
        //Si no se provee ni ancho ni alto, error
        if ($height < 1 && $width < 1) {
            throw new \InvalidArgumentException('You should provide width or height.');
        }
        
        //Si solo se provee ancho, calculamos el alto para que mantenga la rel de aspecto
        if($height < 1){
            $height = (int)($this->height * $width / $this->width);
        }
        //Si solo se provee alto, calculamos el ancho para que mantenga la rel de aspecto
        if($width < 1){
            $width = (int)($this->width * $height / $this->height);
        }
        
        $sized = new Sized();
        $sized->media_id = $this->media_id;
        
        $directory = 'uploads/'.date('Y').'/'.date('m').'/';
            
        FileHelper::createDirectory($directory, 0775, true);
        $path = $directory . uniqid('image') . '.' . $this->extension;
        
        //Modo de generacion de miniatura:
        //http://imagine.readthedocs.org/en/v0.2-0/image.html
        $thumbnailMode = Config::getValue('image_thumbnail_mode_inset') ? 
                \Imagine\Image\ManipulatorInterface::THUMBNAIL_INSET : 
                \Imagine\Image\ManipulatorInterface::THUMBNAIL_OUTBOUND;
        
        \yii\imagine\Image::thumbnail($this->relative_url, $width, $height)->save($path);
        
        $sized->relative_url = $path;
        
        $size = getimagesize($path);
        
        $sized->width = $size[0];
        $sized->height = $size[1];
        
        $sized->save();
        return $sized->url;
        
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSizeds()
    {
        return $this->hasMany(Sized::className(), ['media_id' => 'media_id']);
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
