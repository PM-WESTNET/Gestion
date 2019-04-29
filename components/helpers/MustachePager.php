<?php

namespace app\components\helpers;

/**
 * Description of MustachePager
 *
 * @author mmoyano
 */
class MustachePager extends \yii\base\Component{
    
    public $pagination;
    
    public $maxButtonCount = 10;
    /**
     * @var string|boolean the label for the "next" page button. Note that this will NOT be HTML-encoded.
     * If this property is false, the "next" page button will not be displayed.
     */
    public $nextPageLabel = '>';
    /**
     * @var string|boolean the text label for the previous page button. Note that this will NOT be HTML-encoded.
     * If this property is false, the "previous" page button will not be displayed.
     */
    public $prevPageLabel = '<';
    /**
     * @var string|boolean the text label for the "first" page button. Note that this will NOT be HTML-encoded.
     * If it's specified as true, page number will be used as label.
     * Default is false that means the "first" page button will not be displayed.
     */
    public $firstPageLabel = '<<';
    /**
     * @var string|boolean the text label for the "last" page button. Note that this will NOT be HTML-encoded.
     * If it's specified as true, page number will be used as label.
     * Default is false that means the "last" page button will not be displayed.
     */
    public $lastPageLabel = '>>';
    
    public function getPages(){

        //Para renderizar con mustache
        $pages = [];
        $from = ($this->pagination->page - $this->maxButtonCount/2 >= 0) ? $this->pagination->page - $this->maxButtonCount/2 : 0;
        $to = $from + $this->maxButtonCount > $this->pagination->pageCount ? $this->pagination->pageCount : $from + $this->maxButtonCount;
        
        //First
        if($this->firstPageLabel){
            $pages[] = [
                'label' => $this->firstPageLabel,
                'page' => 1,
                'active' => $this->pagination->page == 0 ? true : false
            ];
        }
        
        //Prev
        if($this->prevPageLabel && $this->pagination->page > 0){
            $pages[] = [
                'label' => $this->prevPageLabel,
                'page' => $this->pagination->page,
                'active' => false
            ];
        }
        
        //Internas
        for( $i = $from; $i < $to; $i++ ){
            $pages[] = [
                'label' => $i+1,
                'page' => $i+1,
                'active' => $i == $this->pagination->page ? true : false 
            ];
        }
        
        //Next
        if($this->nextPageLabel && $this->pagination->page + 2 <= $this->pagination->pageCount){
            $pages[] = [
                'label' => $this->nextPageLabel,
                'page' =>  $this->pagination->page + 2,
                'active' => false
            ];
        }
        
        //Last
        if($this->lastPageLabel){
            $pages[] = [
                'label' => $this->lastPageLabel,
                'page' => $this->pagination->pageCount,
                'active' => $this->pagination->page == $this->pagination->pageCount - 1 ? true : false
            ];
        }
        
        return $pages;
        
    }
    
}
