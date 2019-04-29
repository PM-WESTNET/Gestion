<?php

namespace app\modules\sale\modules\contract\models;
use app\modules\sale\models\Product;

use Yii;

/**
 * This is the model class for table "plan_feature".
 *
 * @property integer $plan_feature_id
 * @property string $name
 * @property string $type
 * @property string $description
 * @property integer $parent_id
 *
 * @property PlanFeature $parent
 * @property PlanFeature[] $planFeatures
 * @property ProductHasPlanFeature[] $productHasPlanFeatures
 * @property Product[] $products
 */
class PlanFeature extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'plan_feature';
    }
    
    /**
     * @inheritdoc
     */
    /*
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['timestamp'],
                ],
            ],
            'date' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date'],
                ],
                'value' => function(){return date('Y-m-d');},
            ],
            'time' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['time'],
                ],
                'value' => function(){return date('h:i');},
            ],
        ];
    }
    */

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['type', 'description'], 'string'],
            [['parent_id'], 'integer'],
            [['parent'], 'safe'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    public function validate($attributeNames = null, $clearErrors = true) {
        parent::validate($attributeNames, $clearErrors);

        if ($this->className() == PlanFeature::className() && empty($this->parent_id) && empty($this->type)) {
            $attribute = 'type';
            $this->addError($attribute, Yii::t('yii', '{attribute} cannot be blank.', ['attribute' => $this->getAttributeLabel($attribute)]));
        }

        return count($this->errors) == 0;
    }

    public function beforeSave($insert) {
        if (!empty($this->parent_id)) {
            $this->type = $this->parent->type;
        }

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'plan_feature_id' => Yii::t('app', 'Plan Feature ID'),
            'name' => Yii::t('app', 'Name'),
            'type' => Yii::t('app', 'Type'),
            'description' => Yii::t('app', 'Description'),
            'parent_id' => Yii::t('app', 'Parent'),
            'parent' => Yii::t('app', 'Parent'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(PlanFeature::className(), ['plan_feature_id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlanFeatures()
    {
        return $this->hasMany(PlanFeature::className(), ['parent_id' => 'plan_feature_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductHasPlanFeatures()
    {
        return $this->hasMany(ProductHasPlanFeature::className(), ['plan_feature_id' => 'plan_feature_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['product_id' => 'product_id'])->viaTable('product_has_plan_feature', ['plan_feature_id' => 'plan_feature_id']);
    }
    
    public static function getOrderedPlanFeatures($parents = []) 
    {
        
        //Si el parametro no es un array, tiro exception
        if (!is_array($parents)) {
            throw new \InvalidArgumentException('Invalid argument. Expected: Array.');
        }

        $nestedPlanFeatures = array();
        
        //Si el padre esta vacio traigo todos los padres
        if (empty($parents)) {
            //Zonas padres absolutas
            $parents = PlanFeature::find()->where('parent_id IS NULL')->all();
        }

        //Recorremos el arreglo de padres para construir el arbol de cada uno
        foreach ($parents as $parent) {
            
            //si no tiene hijos agrego el padre al array
            if (empty($parent->planFeatures))
                $nestedPlanFeatures[] = $parent;
            else {
                //sino agrego el padre y llama nuevamente a la función para que agregue a sus hijos.
                $nestedPlanFeatures[] = $parent;
                $nestedPlanFeatures = array_merge($nestedPlanFeatures, self::getOrderedPlanFeatures($parent->planFeatures));
            }
        }

        return $nestedPlanFeatures;
    }        
                         
    /**
     * @inheritdoc
     * Strong relations: Parent, Products.
     */
    public function getDeletable()
    {
        if($this->getPlanFeatures()->exists()){
            return false;
        }
        if($this->getProducts()->exists()){
            return false;
        }
        return true;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: PlanFeatures, ProductHasPlanFeatures.
     */
    protected function unlinkWeakRelations(){
    }
    
    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if($this->getDeletable()){
                $this->unlinkWeakRelations();
                return true;
            }
        } else {
            return false;
        }
    }
    
    public function getTabName() 
    {
        //Establece una sangría por nivel de Padres de Categoria
        $indent = '&nbsp;&nbsp;&nbsp;&nbsp;';

        // igualo $parent al padre  
        $parent = $this->parent;
        // si $parent esta vació es decir no tiene padre el name queda igual SINO le agrega una sangría con $indent
        $name = empty($parent) ? $this->name : $indent . $this->name;

        if (!empty($parent)){
            do {
                if ($parent = $parent->parent)
                    $name = $indent . $name; //le agrego otra sangría
            }
            while (!empty($parent)); // mientras tenga padre
        }

        return $name;
    }

}
