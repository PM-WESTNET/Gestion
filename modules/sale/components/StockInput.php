<?php

namespace app\modules\sale\components;

use yii\helpers\Html;

/**
 * Description of StockInput
 *
 * @author mmoyano
 */
class StockInput extends \yii\jui\InputWidget{
    
    public $model;
    
    public $attribute;
    
    public $unit;

    public $value;
    
    public $options = [
        'class' => 'form-control'
    ];
    
    public $containerOptions = [
        'class' => 'input-group'
    ];

    public function run(){
        
        return $this->render();
        
    }
    
    public function render($view = null, $params = null)
    {
        
        $contents = [];

        $symbol = $this->unit->symbol;
        $symbolPosition = $this->unit->symbol_position;
        
        if($symbolPosition == 'prefix'){
            $contents[] = Html::tag('span', $symbol, ['class' => 'input-group-addon']);
        }
        
        $contents = array_merge($contents, $this->renderInput());
        
        if($symbolPosition == 'suffix'){
            $contents[] = Html::tag('span', $symbol, ['class' => 'input-group-addon']);
        }
        
        return Html::tag('div', implode("\n", $contents), $this->containerOptions);
        
    }
    
    /**
     * Renderiza un input 
     * @param array $companies
     * @return string
     */
    public function renderInput()
    {
        $contents = [];

        // get formatted date value
        if ($this->hasModel()) {
            $value = Html::getAttributeValue($this->model, $this->attribute);
        } else {
            $value = $this->value;
        }

        $options = $this->options;
        $options['value'] = $value;

        // render a text input
        if ($this->hasModel()) {
            $contents[] = Html::activeTextInput($this->model, $this->attribute, $options);
        } else {
            $contents[] = Html::textInput($this->name, $value, $options);
        }

        return $contents;
        
    }
}
