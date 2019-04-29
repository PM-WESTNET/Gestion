<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\components\companies;

use yii\helpers\ArrayHelper;
use Yii;

/**
 * Extension de DataColumn que muestra, de acuerdo a la configuracion de la app,
 * la empresa asociada al modelo.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class CompanyColumn extends \yii\grid\DataColumn
{
    /**
     * @var string the attribute name associated with this column. When neither [[content]] nor [[value]]
     * is specified, the value of the specified attribute will be retrieved from each data model and displayed.
     *
     * Also, if [[label]] is not specified, the label associated with the attribute will be displayed.
     */
    public $attribute = 'company_id';

    /**
     * @var boolean whether to allow sorting by this column. If true and [[attribute]] is found in
     * the sort definition of [[GridView::dataProvider]], then the header cell of this column
     * will contain a link that may trigger the sorting when being clicked.
     */
    public $enableSorting = true;
    
    public $format = ['html'];
    
    public function init()
    {
        //Lo importante de esta clase es verificar si debe o no mostrarse de acuerdo a la config
        $this->visible = Yii::$app->params['companies']['enabled'];
        
        //Usamos CompanySelector, que resuelve algunos detalles relacionados con User y devuelve los valores mapeados
        $companies = CompanySelector::findCompanies();
        $this->filter = $companies;
        
        $this->label = Yii::t('app', 'Company');
        
        $this->value = function($model){ 
            if($model->company){
                return \yii\helpers\Html::a($model->company->name, ['/sale/company/view', 'id'=>$model->company_id] );
            }else{
                return null;
            }
        };
        
    }
}
