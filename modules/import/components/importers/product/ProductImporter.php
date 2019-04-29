<?php

/**
 * Description of ProductImporter
 *
 * @author martin
 */
class ProductImporter {
    
    public function import($file)
    {
        
        /**
         * Microsoft excel guarda los archivos con extension csv como archivos
         * de tipo application/vnd.ms-excel
         */
        if($file->type=='text/csv' || $file->type=='application/vnd.ms-excel'){
            require(__DIR__.'/CSVProductImporter.php');
            $importer = new CSVProductImporter();
            
            $count = $importer->import($file);
            
            return $count;
        }else{
            throw new \yii\web\HttpException(500, Yii::t('import','Invalid file format.'));
        }
        
    }
    
}
