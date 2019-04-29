<?php

namespace app\modules\sale\models;

use Yii;

/**
 * This is the model class for table "category".
 *
 * @property integer $category_id
 * @property string $name
 * @property string $status
 * @property string $system
 * @property integer $parent_id
 *
 * @property Category $parent
 * @property Category[] $categories
 * @property ProductHasCategory[] $productHasCategories
 * @property Product[] $products
 */
class Category extends \app\components\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category';
    }
    
    public function behaviors()
    {
        return [
            'slug'=>[
                'class' => \yii\behaviors\SluggableBehavior::className(),
                'slugAttribute' => 'system',
                'attribute' => 'name',
                'ensureUnique'=>true,
                'immutable' => true
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['status'], 'in', 'range'=>['enabled','disabled']],
            [['parent_id'], 'integer'],
            [['name'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'category_id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'status' => Yii::t('app', 'Status'),
            'system' => Yii::t('app', 'System'),
            'parent_id' => Yii::t('app', 'Parent Category'),
            'parent' => Yii::t('app', 'Parent Category'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Category::className(), ['category_id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['parent_id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductHasCategories()
    {
        return $this->hasMany(ProductHasCategory::className(), ['category_id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['product_id' => 'product_id'])->viaTable('product_has_category', ['category_id' => 'category_id']);
    }
    
    /**
     * Recursivo. Devuelve un arraglo ordenado de categorias padre > hijos
     * @param Category $parents
     * @return array Arreglo jerarquizado de categorias
     */
    public static function getOrderedCategories($parents = []) 
    {
        
        //Si el parametro no es un array, tiro exception
        if (!is_array($parents)) {
            throw new \InvalidArgumentException('Invalid argument. Expected: Array.');
        }

        $nestedCategories = array();
        
        //Si el padre esta vació traigo todos los padres
        if (empty($parents)) {
            //Categorias padres absolutas
            $parents = Category::find()->where('parent_id IS NULL')->all();
        }

        //Recorremos el arraglo de padres para construir el arbol de cada uno
        foreach ($parents as $parent) {
            
            //si no tiene hijos agrego el padre al array
            if (empty($parent->categories))
                $nestedCategories[] = $parent;
            else {
                //sino agrego el padre y llama nuevamente a la función para que agregue a sus hijos.
                $nestedCategories[] = $parent;
                $nestedCategories = array_merge($nestedCategories, self::getOrderedCategories($parent->categories));
            }
        }

        return $nestedCategories;
    }
    
    /**
     * Recursivo. Devuelve un arraglo ordenado de categorias padre > hijos
     * @param Category $parents
     * @return array Arreglo jerarquizado de categorias
     */
    //TODO -> borrar, aparentemente no se esta usando
    public static function getNestedCategories($parents = []) 
    {
        
        //Si el parametro no es un array, tiro exception
        if (!is_array($parents)) {
            throw new \InvalidArgumentException('Invalid argument. Expected: Array.');
        }

        $nestedCategories = array();
        
        //Si el padre esta vació traigo todos los padres
        if (empty($parents)) {
            //Categorias padres absolutas
            $parents = Category::find()->where('parent_id IS NULL')->all();
        }

        //Recorremos el arraglo de padres para construir el arbol de cada uno
        foreach ($parents as $parent) {
            
            //si no tiene hijos agrego el padre al array
            if (empty($parent->categories))
                $nestedCategories[] = [$parent];
            else {
                //sino agrego el padre y llama nuevamente a la función para que agregue a sus hijos.
                $nestedCategories[] = [
                    $parent,
                    'children'=>array_merge($nestedCategories, self::getNestedCategories($parent->categories))
                ];
            }
        }

        return $nestedCategories;
    }
    
    
    /**
     * Devuelve el nombre de un label con sangría para indicar la jerarquia que tiene en el arbol visualmente
     * @return string #
     */
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
    
    public function getDeletable(){
        
        if($this->getCategories()->exists()){
            return false;
        }
        
        if($this->getProducts()->exists()){
            return false;
        }
        
        return true;
        
    }
    
    /**
     * Establece los atributos que deben ser utilizados al momento de convertir
     * el objeto a array
     * @return type
     */
    public function fields() 
    {
        
        return [
            'category_id',
            'name',
            'system',
            'status',
        ];
        
    }

}
