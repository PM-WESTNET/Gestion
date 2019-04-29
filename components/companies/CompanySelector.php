<?php

namespace app\components\companies;

use app\modules\sale\models\Company;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * Description of CompanySelector
 * CompanySelector renderiza un selector de empresas de acuerdo a la config
 * de arya: params['companies']
 * 
 * Si no se utiliza multiple empresa, el selector renderizara un input hidden
 * con el id de la empresa por defecto.
 * 
 * @author mmoyano
 */
class CompanySelector extends \yii\base\Widget{
    
    /**
     * Condiciones para el query de busqueda de empresas
     * @var array 
     */
    public $conditions = ['status' => 'enabled'];

    /**
     * Tipo de input. Tipos soportados:
     * - dropdown: Genera un dropDownList
     * - radio: Genera un radioList
     * - checkbox: Genera un checkboxList
     * @var string 
     */
    public $inputType = 'dropdown';

    /**
     * Indica si en caso de venir vacio el company_id, se debe seleccionar la empresa por defecto.
     * @var bool
     */
    public $setDefaultCompany = true;

    /**
     * Modelo
     * @var Object 
     */
    public $model;
    
    /**
     * Label
     * @var Object 
     */
    public $label;
    
    /**
     * Atributo, por defecto "company_id"
     * @var string 
     */
    public $attribute = 'company_id';
    
    /**
     * Valor/es seleccionado/s
     * @var mixed
     */
    public $selection;
    
    /**
     * Opciones adicionales para generar el input. Si no se especifica clase,
     * por defecto se utilizara la clase 'form-control'
     * @var type 
     */
    public $options;
    
    /**
     * Opciones adicionales para generar el input. Si no se especifica clase,
     * por defecto se utilizara la clase 'form-control'
     * @var type 
     */
    public $inputOptions;
    
    /**
     * @var string the template that is used to arrange the label, the input field, the error message and the hint text.
     * The following tokens will be replaced when [[render()]] is called: `{label}`, `{input}`, `{error}` and `{hint}`.
     */
    public $template = "{label}\n{input}\n{hint}\n{error}";
    
    /**
     * @var array the default options for the error tags. The parameter passed to [[error()]] will be
     * merged with this property when rendering the error tag.
     * The following special options are recognized:
     *
     * - tag: the tag name of the container element. Defaults to "div".
     * - encode: whether to encode the error output. Defaults to true.
     *
     * If you set a custom `id` for the error element, you may need to adjust the [[$selectors]] accordingly.
     *
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $errorOptions = ['class' => 'help-block'];
    
    /**
     * @var array the default options for the label tags. The parameter passed to [[label()]] will be
     * merged with this property when rendering the label tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $labelOptions = ['class' => 'control-label'];

    /**
     * @var array the default options for the hint tags. The parameter passed to [[hint()]] will be
     * merged with this property when rendering the hint tag.
     * The following special options are recognized:
     *
     * - tag: the tag name of the container element. Defaults to "div".
     *
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $hintOptions = ['class' => 'hint-block'];
    
    public $parts = [];
    
    public $form;
    
    public $hidden = false;

    /**
     * @var array
     * Indica que empresas mostrar
     * Opciones: Mostrar solo las padres (parent)
     *           Mostrar solo las hijas (children)
     *           Mostrar todas (all)
     */
    public $showCompanies = 'all';


    public function run(){

        if (isset($this->model->company_id) && empty($this->model->company_id) && $this->setDefaultCompany) {
            $this->model->company_id= $this->getDefaultCompany();
        }
        
        if(Yii::$app->params['companies']['enabled'] === false || $this->hidden){
            return $this->hiddenField();
        }

        $companies = self::findCompanies($this->showCompanies, $this->conditions);
        
        $this->initOptions();
    
        return $this->render($companies);
        
    }
    
    /**
     * Renderiza un input para company_id con label, error y hint de acuerdo a las opciones.
     * @param array $companies
     * @return string
     */
    public function render($companies, $params = [])
    {


        if($this->model){
            $this->parts['{input}'] = $this->renderActiveInput($companies);
        }else{
            $this->parts['{input}'] = $this->renderInput($companies);
        }
        
        if (!isset($this->parts['{label}'])) {
            $this->label();
        }
        if (!isset($this->parts['{error}'])) {
            $this->error();
        }
        if (!isset($this->parts['{hint}'])) {
            $this->hint(null);
        }
        
        $content = strtr($this->template, $this->parts);
        
        return $this->before() . "\n" . $content . "\n" . $this->after();
        
    }
    
    /**
     * Generates a label tag for [[attribute]].
     * @return $this the field object itself
     */
    public function label($label = null, $options = [])
    {
        if ($this->label === false) {
            $this->parts['{label}'] = '';
            return $this;
        }
        $options = array_merge($this->labelOptions, $options);
        if ($this->label !== null) {
            $options['label'] = $this->label;
        }else{
            $this->label = Yii::t('app', 'Company');
        }
        if($this->model && isset($this->model->attributeLabels()[$this->attribute])){
            $this->parts['{label}'] = Html::activeLabel($this->model, $this->attribute, $options);
        }else{
            $this->parts['{label}'] = Html::label($this->label, $this->attribute, $options);
        }
        return $this;
    }
    
    /**
     * Generates a tag that contains the first validation error of [[attribute]].
     * Note that even if there is no validation error, this method will still return an empty error tag.
     * @param array|boolean $options the tag options in terms of name-value pairs. It will be merged with [[errorOptions]].
     * The options will be rendered as the attributes of the resulting tag. The values will be HTML-encoded
     * using [[Html::encode()]]. If a value is null, the corresponding attribute will not be rendered.
     *
     * The following options are specially handled:
     *
     * - tag: this specifies the tag name. If not set, "div" will be used.
     *
     * If this parameter is false, no error tag will be rendered.
     *
     * If you set a custom `id` for the error element, you may need to adjust the [[$selectors]] accordingly.
     *
     * @return $this the field object itself
     */
    public function error($options = [])
    {
        if($this->model){
            if ($options === false) {
                $this->parts['{error}'] = '';
                return $this;
            }
            $options = array_merge($this->errorOptions, $options);

            $this->parts['{error}'] = Html::error($this->model, $this->attribute, $options);
        }else{
            $this->parts['{error}'] = '';
        }
        return $this;
    }
    /**
     * Renders the hint tag.
     * @param string $content the hint content. It will NOT be HTML-encoded.
     * @param array $options the tag options in terms of name-value pairs. These will be rendered as
     * the attributes of the hint tag. The values will be HTML-encoded using [[Html::encode()]].
     *
     * The following options are specially handled:
     *
     * - tag: this specifies the tag name. If not set, "div" will be used.
     *
     * @return $this the field object itself
     */
    public function hint($content, $options = [])
    {
        $options = array_merge($this->hintOptions, $options);
        $options['hint'] = $content;
        if($this->model){
            $this->parts['{hint}'] = Html::activeHint($this->model, $this->attribute, $options);
        }else{
            $this->parts['{hint}'] = '';
        }
        return $this;
    }
    
    /**
     * Busca las empresas a mostar en el input, de acuerdo a la configuracion.
     * Si se ha configurado la opcion byUser como true, se mostraran las empresas
     * asignadas al usuario, si no tiene ninguna asignada, se mostrarán todas las empresas.
     * Si se ha configurado byUser como false, se mostraran todas las empresas.
     *
     * @return array
     */
    public static function findCompanies($showCompanies = 'all', $conditions = ['status' => 'enabled'])
    {
        $array = [];

        if(Yii::$app->params['companies']['byUser'] === false) {
            $companies_ids = Company::find()->select('company_id')->where($conditions)->asArray()->all();
        } else {
            $companies_ids = Yii::$app->user->identity->getCompanies()->select('company_id')->where($conditions)->asArray()->all();

            //Si el usuario no tiene asignada ninguna empresa, se muestran todas.
            if(empty($companies_ids)){
                $companies_ids = Company::find()->select('company_id')->where($conditions)->asArray()->all();
            }
        }

        foreach ($companies_ids as $value) {
            array_push($array, $value['company_id']);
        }

        //Muestro solo las que se indican por el parametro showCompanies
        if($showCompanies){
            $query = Company::find()->where(['in', 'company_id', $array]);
            if($showCompanies == 'parent'){
                $companies = $query->andWhere('parent_id IS NULL')->all();
            }
            if($showCompanies == 'children'){
                $companies = $query->andWhere('parent_id IS NOT NULL')->all();
            }
            if($showCompanies == 'all'){
                $companies = $query->orWhere(['in', 'parent_id', $array])->all();
            }
        } else {
            $companies = Company::find()->where(['in', 'company_id', $array])->all();
        }
        
        $companies = ArrayHelper::map($companies, 'company_id', 'name');
        
        return $companies;
    }
    
    private function initOptions()
    {
        
        if(!isset($this->inputOptions['class'])){
            $this->inputOptions['class'] = 'form-control';
        }
        
    }
    
    /**
     * Genera un input (no active) de tipo dropdown list o checkbox list o radio list,
     * con una lista de empresas
     * @param array $companies
     * @return string
     * @throws \yii\web\HttpException
     */
    private function renderInput($companies)
    {
        
        $types = [
            'dropdown' => 'dropDownList',
            'checkbox' => 'checkboxList',
            'radio' => 'radioList'
        ];
        
        if(!key_exists($this->inputType, $types) ){
            throw new \yii\web\HttpException(500, 'Invalid input type.');
        }
        
        $type = $types[$this->inputType];
        
        return Html::$type($this->attribute, $this->selection, $companies, $this->inputOptions);
        
    }
    
    /**
     * Genera un active input de tipo dropdown list o checkbox list o radio list,
     * con una lista de empresas
     * @param array $companies
     * @return string
     * @throws \yii\web\HttpException
     */
    private function renderActiveInput($companies)
    {
        
        $types = [
            'dropdown' => 'activeDropDownList',
            'checkbox' => 'activeCheckboxList',
            'radio' => 'activeRadioList'
        ];
        
        if(!key_exists($this->inputType, $types) ){
            throw new \yii\web\HttpException(500, 'Invalid input type.');
        }
        
        $type = $types[$this->inputType];
        
        return Html::$type($this->model, $this->attribute, $companies, $this->inputOptions);
        
    }
    
    /**
     * Genera un campo oculto con el id de la empresa por defecto
     * @return string
     * @throws \yii\web\HttpException
     */
    private function hiddenField()
    {
        //La idea es generar un hidden field con la unica empresa que deberia ser la empresa por defecto
        $company = $this->getDefaultCompany();
        
        if(!$company){
            throw new \yii\web\HttpException(500, 'At least one company required.');
        }
        
        if($this->model){
            return Html::hiddenInput(Html::getInputName($this->model, $this->attribute), $company);
        }else{
            return Html::hiddenInput($this->attribute, $company->company_id);
        }
    }
    
    /**
     * Renders the opening tag of the field container.
     * @return string the rendering result.
     */
    public function before()
    {
        $inputID = 'company_id';
        $attribute = Html::getAttributeName($this->attribute);
        $options = $this->options;
        $class = isset($options['class']) ? [$options['class']] : ['form-group'];
        $class[] = "field-$inputID";
        if($this->model && $this->form){
            if ($this->model->isAttributeRequired($attribute)) {
                $class[] = $this->form->requiredCssClass;
            }
            if ($this->model->hasErrors($attribute)) {
                $class[] = $this->form->errorCssClass;
            }
        }elseif($this->model){
            if ($this->model->isAttributeRequired($attribute)) {
                $class[] = 'required';
            }
            if ($this->model->hasErrors($attribute)) {
                $class[] = 'has-error';
            }
        }
        $options['class'] = implode(' ', $class);
        $tag = ArrayHelper::remove($options, 'tag', 'div');
        return Html::beginTag($tag, $options);
    }
    /**
     * Renders the closing tag of the field container.
     * @return string the rendering result.
     */
    public function after()
    {
        return Html::endTag(isset($this->options['tag']) ? $this->options['tag'] : 'div');
    }
    
    private function getDefaultCompany(){
        $default= Company::findOne(['default' => 1]);
        
        return $default->company_id;
    }
    
}
