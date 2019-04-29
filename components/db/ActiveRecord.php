<?php

namespace app\components\db;

/**
 * Description of ActiveRecord
 *
 * @author mmoyano
 */
class ActiveRecord extends \app\modules\log\db\ActiveRecord{
    
    public function getDeletable(){
        
        return false;
        
    }
    
}
