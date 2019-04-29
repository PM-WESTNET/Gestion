<?php

namespace app\modules\media\components\view; 

use yii\helpers\Url;
use yii\helpers\Html;
use Yii;

/**
 * Description of UploadWidgetr
 *
 * @author martin
 */
class Preview extends \yii\jui\Widget{
    
    public $enableTitle;
    public $enableDescription;
    
    public $media;
    
    public $update = false;
    
    public $containerOptions = [];
    
    public $width = 300;
    public $height = 300;
    
    public function init()
    {

        if(!isset($this->containerOptions['class'])){
            $this->containerOptions['class'] = 'col-md-3 col-xs-6';
        }
        
        if($this->update && $this->view){
            \app\modules\media\components\upload\UploadAsset::register($this->view);
        }
        
    }
    
    public function run()
    {
        return $this->renderPreview();
    }
    
    public function renderPreview()
    {
        
        $content = $this->media->render($this->width, $this->height);

        if($this->update == true){
            
            $content = Html::tag('div', $content, ['class' => 'update-preview-img-container']);
            
            $buttons = Html::a('<span class="glyphicon glyphicon-remove"></span>'.
                Yii::t('yii', 'Delete'), null, ['class' => 'btn btn-danger', 'data-media-delete' => '']);
            
            $content .= Html::tag('div', Html::tag('p', $buttons), ['class' => 'caption']);
        }

        $content .= Html::hiddenInput('Media[]', $this->media->media_id);

        $thumbnail = Html::tag('div', $content, ['class' => 'thumbnail']);

        $this->containerOptions['data-media'] = '';
        return Html::tag('div', $thumbnail, $this->containerOptions);
        
    }

}