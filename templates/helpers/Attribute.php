<?php

namespace app\templates\helpers;

/**
 * Attribute is a representative class for ColumnSchema columns, useful to know types and build strings for gii's code generation
 *
 * @author smaldonado
 */
class Attribute {

    public $analizer;
    public $builder;
    private $column;

    public function __construct(\yii\db\ColumnSchema $column) {

        $this->analizer = new AttributeAnalizer($column);
        $this->builder = new AttributeBuilder($column);
        $this->column = $column;
    }

    public function getColumn() {

        return $this->column;
    }

}
