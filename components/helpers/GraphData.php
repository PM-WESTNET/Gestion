<?php

namespace app\components\helpers;

/**
 * Description of GraphData
 *
 * @author mmoyano
 */
class GraphData extends \yii\base\BaseObject{
    
    public $fromdate;
    public $todate;
    public $interval = 'P1D';
    
    public $dataProvider;
    
    public $xAttribute;
    public $yAttribute;
    public $idAttribute;
    
    public $colorAttribute;
    
    public $steps;
    
    public function getSteps()
    {
        
        if($this->steps) return $this->steps;
        
        $interval = $this->getInterval();
        
        if($this->fromdate == null || $this->todate == null){
            return [];
        }
        
        $fromdate = \DateTime::createFromFormat('Y-m-d', $this->fromdate);
        $todate = \DateTime::createFromFormat('Y-m-d', $this->todate);
        
        $periods = new \DatePeriod(
            $fromdate,
            new \DateInterval($interval),
            $todate->modify('+1 day')
        );
        
        $data = [];
        foreach ($periods as $period){
            $data[] = $period->format('Y-m-d');
        }
        
        return $data;
    }
    
    public function getDatesDiff()
    {
        
        $date1 = new DateTime($this->fromdate);
        $date2 = new DateTime($this->todate);

        return $date2->diff($date1)->format("%a");
        
    }
    
    public function getInterval()
    {
        
        if($this->interval == 'auto'){
            
            //Obtenemos la cantidad de dias entre la fecha de inicio y fin
            $days = $this->getDatesDiff();
            
            if($days > 30){
                $periodDays = (int) $days / 30;
                return "P{$periodDays}D";
            }else{
                return "P1D";
            }
        }
        
        return $this->interval;
        
    }
    
    public function getDatasets()
    {
        
        $models = $this->dataProvider->getModels();
        
        //Separamos productos
        $items = [];
        $colors = [];
        $itemsData = [];
        foreach ($models as $model){
            
            $item_id = $model->{$this->idAttribute};
            
            if(isset($itemsData[$item_id])){
                $itemsData[$item_id][$model->{$this->xAttribute}] = (float)$model->{$this->yAttribute};
            }else{
                $itemsData[$item_id] = [ $model->{$this->xAttribute} => (float)$model->{$this->yAttribute}];
                $items[] = $item_id;
                $colors[$item_id] = $model->{$this->colorAttribute};
            }
        }
        
        $steps = $this->getSteps();
        
        //Establecemos por cada producto, el stock de cada periodo
        $data = [];
        foreach($items as $item){
            
            $data[$item] = [];
            
            //Ultimo valor valido
            $lastValue = 0.0;
            
            foreach($steps as $step){
                
                if(isset($itemsData[$item][$step])){
                    $lastValue = $itemsData[$item][$step];
                    $data[$item][$step] = $lastValue;
                }else{
                    $data[$item][$step] = $lastValue;
                }
            }
            
            $data[$item] = array_values($data[$item]);
        }
        
        $datasets = [];
        foreach($data as $item_id=>$set){

            $rgb = $colors[$item_id];

            $datasets[] = [
                'label' => 'Monto de comprobantes',
                'fillColor' => "rgba($rgb,0.5)",
                'strokeColor' => "rgba($rgb,1)",
                'pointColor' => "rgba($rgb,1)",
                'pointStrokeColor' => "#444",
                'data' => $set
            ];
        }
        
        return $datasets;
        
    }
    
}
