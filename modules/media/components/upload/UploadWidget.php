<?php

namespace app\modules\media\components\upload; 

use yii\helpers\Url;
use yii\helpers\Html;
use Yii;
use app\modules\media\components\view\Preview;
use yii\jui\Widget;

/**
 * Description of UploadWidgetr
 *
 * @author martin
 */
class UploadWidget extends Widget{
    
    public $enableTitle;
    public $enableDescription;
    
    public $media = [];
    
    public $inputId = 'fileupload';
    
    public $buttonOptions = ['class' => 'btn btn-success fileinput-button'];
    
    public $label;
    
    public $attribute = 'Media[file]';
    
    public $type = 'image';
    
    public $previewContainerOptions;
    
    public function init()
    {
        UploadAsset::register($this->view);
        $this->view->registerJs('UploadWidget.init({url: "'.Url::to(["/media/$this->type/create"]).'", inputId: "'.$this->inputId.'"});');
        
        if(empty($this->label)){
            $this->label = '<i class="glyphicon glyphicon-plus"></i> '.Yii::t('app', 'Select');
        }
    }
    
    public function run()
    {
        $content = Html::tag('div', Html::tag('div', null, ['class' => 'col-lg-12', 'data-messages' => '']), ['class' => 'row']);
        $content .= $this->renderInput();
        $content .= $this->renderProgressBar();
        $content .= $this->renderPreview();
        
        return $content;
    }
    
    public function renderInput()
    {
        
        $input = Html::fileInput($this->attribute, null, ['multiple' => '', 'id' => $this->inputId ]);
        
        return Html::tag('span', $this->label . $input, $this->buttonOptions);
        
    }
    
    public function renderProgressBar()
    {
        return '<br/><br/><div id="progress" class="progress">
            <div class="progress-bar progress-bar-success"></div>
        </div>';
    }
    
    public function renderPreview()
    {
        $preview = '';
        
        foreach($this->media as $media){
            $preview .= Preview::widget([
                'media' => $media,
                'update' => true,
                'width' => 300,
                'height' => 300,
                'containerOptions' => $this->previewContainerOptions
            ]);
        }
        
        return Html::tag('div', $preview, ['class' => 'row', 'data-media-preview-list' => '']);
        
    }

}
