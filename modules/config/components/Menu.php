<?php

namespace app\modules\config\components;

use Yii;

/**
 * Description of Menu
 *
 * @author mmoyano
 */
class Menu {
    
    /**
     * Devuelve una lista de items con urls de configuraciones basado en 
     * categorias de configuracion para ser utilizado en un widget Menu
     */
    public static function items()
    {
        
        $items = [];
        
        $categories = \app\modules\config\models\Category::find()->where(['status' => 'enabled'])->all();
        
        foreach($categories as $category){
            
            if(!$category->superadmin || Yii::$app->user->isSuperadmin){
                //Formato de item de \yii\bootstrap\Nav
                $items[] = [
                    'label' => $category->name,
                    'url' => ['/config/config', 'category' => $category->category_id],
                ];
            }
        }
        
        return $items;
        
    }
    
}
