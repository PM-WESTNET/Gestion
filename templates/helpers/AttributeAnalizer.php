<?php

namespace app\templates\helpers;

/**
 * Description of AttributeAnalizer
 *
 * @author smaldonado
 */
class AttributeAnalizer {

    const TYPE_TINYINT = 'tinyint(1)';

    private $column;

    public function __construct(\yii\db\ColumnSchema $column) {

        $this->column = $column;
    }

    /**
     * Checks if a column is a defined boolean on database schema
     * @return boolean
     */
    public function isBoolean() {

        if ($this->column->phpType === \yii\db\oci\Schema::TYPE_INTEGER && $this->column->dbType === self::TYPE_TINYINT || $this->column->phpType === \yii\db\oci\Schema::TYPE_BOOLEAN)
            return true;
        else
            return false;
    }

    /**
     * Checks if a column is a primary key or not
     * @return boolean
     */
    public function isPrimaryKey() {

        if ($this->column->isPrimaryKey)
            return true;
        else
            return false;
    }

    /**
     * @brief Checks if a column is a relation from a given group of relations
     * @param [] $relations
     * @return boolean
     */
    public function isToBuildRelation($relations = []) {

        $return = false;

        //Si hay relacionse seguimos, sino, decimos que este attr no es una relacion a construirse
        if (!empty($relations)) {

            foreach ($relations as $relation) {

                //Si la relacion se construye, verificamos que este attr no sea esta relacion
                if ($relation['implementation']['build'] == 1) {

                    if ($relation['foreignKey'] == $this->column->name)
                        return true;
                }else {

                    $return = false;
                }
            }
        } else {

            $return = false;
        }

        return $return;
    }

    /**
     * @brief Returns a relation where this attribute is foreign key
     * @param type $relations
     * @return boolean
     */
    public function getRelation($relations = []) {

        if (!empty($relations)) {

            foreach ($relations as $name => $relation) {

                //Si la relacion se construye, verificamos que este attr no sea esta relacion
                if ($relation['implementation']['build'] == 1) {

                    if ($relation['foreignKey'] == $this->column->name) {
                        $return = $relation;
                        $return['name'] = $name;
                        return $return;
                    }
                } else {

                    $return = [];
                }
            }
        } else {

            $return = false;
        }

        return $return;
    }

}
