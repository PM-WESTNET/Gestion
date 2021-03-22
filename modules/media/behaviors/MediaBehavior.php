<?php

namespace app\modules\media\behaviors;

use Yii;
use app\modules\media\models\Media;
use app\modules\media\models\ModelHasMedia;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Description of MediaBehavior
 * MediaBehaviour debe ser incorporado en una clase que requiera Multimedia.
 * Agrega un setter y un getter para media (getMedia y setMedia).
 * Escucha el evento onAfterSave para guardar los recursos multimedia vinculados
 * al objeto.
 *
 * @author martin
 */
class MediaBehavior extends Behavior{
    
    private $_media = [];
    
    /**
     * Si se deben capturar los eventos EVENT_AFTER_UPDATE y EVENT_AFTER_INSERT
     * para intentar obtener automaticamente los datos a asociar al modelo
     * desde POST('Media')
     */
    public $captureEvents = true;
    
    public function events()
    {
        if($this->captureEvents == false){
            return [];
        }
        
        return [
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
        ];
    }
    
    /**
     * Establece una relación entre el modelo y media
     * @return Query
     * @throws \yii\web\HttpException
     */
    public function getMedia(){
        
        $key = $this->owner->primaryKey()[0];
        
        if(is_array($key)){
            throw new \yii\web\HttpException(500, 'Array keys not supported.');
        }
        
        //Realizamos un join, dado que necesitamos ordenar por un campo de la tabla usada para junction
        $query = Media::find();
        $query->where(['status' => 'enabled']);
        $query->multiple = true;
        $query->innerJoin('model_has_media', 'model_has_media.media_id = media.media_id AND model_has_media.model = :model', ['model' => $this->owner->className()]);
        $query->andWhere(['model_has_media.model_id' => $this->owner->primaryKey]);
        $query->orderBy(['model_has_media.order' => SORT_ASC]);
        
        return $query;
        
//      No ordena por junction table:
//        return $this->owner->hasMany(Media::className(), ['media.media_id' => 'media_id'])
//                ->viaTable('media.model_has_media', ['model_id' => $key], function($query){ 
//                    $query->where(['model' => $this->owner->className()]); 
//                });
        
    }
    
    /**
     * Setea los recursos multimedia al modelo
     * @param array $media
     * @throws \yii\web\HttpException
     */
    public function setMedia($media){
        
        $key = $this->owner->primaryKey;
        $class = $this->owner->className();
        
        if(is_array($key)){
            throw new \yii\web\HttpException(500, 'Array keys not supported.');
        }
        
        if(is_array($media)){
            
            foreach ($media as $m){
                $this->_media[] = $m;
            }
            
        } else {
            //setMedia es una asignador masivo
            throw new \yii\web\HttpException(500, 'First param should be an array.');
            
        }      
        
        //Quitamos las relaciones actuales
        $mhm = ModelHasMedia::deleteAll(['model_id'=>$key, 'model'=>$class]);

        //Guardamos las nuevas relaciones
        foreach ($this->_media as $order=>$m){
            $mhm = new ModelHasMedia();

            $mhm->media_id = $m->media_id;
            $mhm->model_id = $key;
            $mhm->model = $class;
            $mhm->order = $order;

            $mhm->save();
        }
        
    }
    
    /**
     * Esta funcion es llamada en los eventos ActiveRecord::EVENT_AFTER_UPDATE y
     * ActiveRecord::EVENT_AFTER_INSERT. Busca Media en post y en caso de encontrar
     * datos, intenta setear los recursos multimedia al modelo que implementa
     * el behaviour.
     * @param type $event
     */
    public function afterSave($event){

        if (!Yii::$app instanceof Yii\console\Application){
            $mediaIds = \Yii::$app->request->post('Media');
            if(is_array($mediaIds)){
                $media = array_map(function($id){
                    $item = Media::findOne($id);
                    if($item === null){
                        throw new \yii\web\HttpException(500, Yii::t('media', 'Media not found.'));
                    }
                    return $item;
                }, $mediaIds);
            }else{
                $media = [];
            }
        
        $this->setMedia($media);
        }
    }
    
}
