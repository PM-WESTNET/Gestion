<?php

namespace app\modules\sale\models;

use Yii;

/**
 * This is the model class for table "customer_category".
 *
 * @property integer $customer_category_id
 * @property string $name
 * @property string $status
 * @property integer $parent_id
 *
 * @property CustomerCategory $parent
 * @property CustomerCategory[] $customerCategories
 * @property CustomerCategoryHasCustomer[] $customerCategoryHasCustomers
 */
class CustomerCategory extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_category';
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
            [['name', 'status'], 'required'],
            [['status'], 'string'],
            [['parent_id'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'customer_category_id' => Yii::t('app', 'Customer Category'),
            'name' => Yii::t('app', 'Name'),
            'status' => Yii::t('app', 'Status'),
            'parent_id' => Yii::t('app', 'Parent'),
            'customerCategories' => Yii::t('app', 'CustomerCategories'),
            'customerCategoryHasCustomers' => Yii::t('app', 'CustomerCategoryHasCustomers'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(CustomerCategory::className(), ['customer_category_id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerCategories()
    {
        return $this->hasMany(CustomerCategory::className(), ['parent_id' => 'customer_category_id']);
    }

    public static function getOrderedCustomerCategories($parents = []) 
    {
        
        //Si el parametro no es un array, tiro exception
        if (!is_array($parents)) {
            throw new \InvalidArgumentException('Invalid argument. Expected: Array.');
        }

        $nestedCustomerCategory = array();
        
        //Si el padre esta vacio traigo todos los padres
        if (empty($parents)) {
            //Zonas padres absolutas
            $parents = CustomerCategory::find()->where('parent_id IS NULL')->all();
        }

        //Recorremos el arreglo de padres para construir el arbol de cada uno
        foreach ($parents as $parent) {
            
            //si no tiene hijos agrego el padre al array
            if (empty($parent->customerCategories))
                $nestedCustomerCategory[] = $parent;
            else {
                //sino agrego el padre y llama nuevamente a la función para que agregue a sus hijos.
                $nestedCustomerCategory[] = $parent;
                $nestedCustomerCategory = array_merge($nestedCustomerCategory, self::getOrderedCustomerCategories($parent->customerCategories));
            }
        }

        return $nestedCustomerCategory;
    }    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerCategoryHasCustomers()
    {
        return $this->hasMany(CustomerHasCategory::className(), ['customer_category_id' => 'customer_category_id']);
    }
    
    /**
     * @inheritdoc
     * Strong relations: CustomerCategories, CustomerCategoryHasCustomers.
     */
    public function getDeletable()
    {
        if($this->getCustomerCategories()->exists()){
            return false;
        }
        if($this->getCustomerCategoryHasCustomers()->exists()){
            return false;
        }

        return true;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: Parent.
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
