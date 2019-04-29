<?php

namespace app\templates\helpers;

/**
 * Represents a database relation between two tables used in model generation
 *
 * @author smaldonado
 */
class Relation {

    const TYPE_HAS_ONE = 'hasOne';
    const TYPE_HAS_MANY = 'hasMany';
    const TYPE_MANY_MANY = 'manyMany';
    const DROPDOWNLIST = 'dropdownList';
    const RADIOLIST = 'radioList';
    const CHECKBOXLIST = 'checkboxList';
    const RECURSIVE_CHECKBOXLIST = 'recursiveCheckboxList';
    const RECURSIVE_RADIOLIST = 'recursiveRadioList';

    private $relation;
    private $options;
    //Relation attrs
    private $return;
    private $name;
    private $unknown;
    private $model;
    private $type;
    private $foreignKey;
    //Options attrs
    private $build;
    private $strength;
    private $representation;
    private $namespace;

    /**
     * @brief Builds an specific relation
     * @param string $relationName
     * @return string
     */
    public function buildRelation($relationName) {

        $relation = [];
        $relation['implementation'] = [];

        //Name
        $this->name = $relationName;
        $functionName = 'build' . ucfirst($this->type);

        //Basic attrs
        $relation['0'] = $this->return;
        $relation['1'] = $this->model;
        $relation['2'] = $this->unknown;
        $relation['type'] = $this->type;
        $relation['model'] = $this->model;

        //Relation attrs
        $relation['foreignKey'] = $this->foreignKey;

        $relation['implementation'] = $this->$functionName();
        $relation['implementation']['build'] = $this->build;
        $relation['implementation']['isDeleteable'] = $this->checkDeleteable();
        $relation['implementation']['representation'] = $this->checkRepresentation();
        $relation['implementation']['namespace'] = $this->namespace;

        return $relation;
    }

    /**
     * @brief Returns default implementations
     * @return []
     */
    public function buildHasOne() {

        $implementations = [];

        return $implementations;
    }

    /**
     * @brief Returns default implementations
     * @return []
     */
    public function buildHasMany() {

        $implementations = [];

        return $implementations;
    }

    /**
     * @brief Returns default implementations
     * @return []
     */
    public function buildManyMany() {

        $implementations = [];

        return $implementations;
    }

    /**
     * @brief Sets options choosen by user for this relation
     * @param type $options
     */
    public function setOptions($options) {

        $this->options = $options;

        $this->build = $this->options['build'];

        if (isset($this->options['strength'])) {
            $this->strength = $this->options['strength'];
        }

        if (isset($this->options['representation'])) {
            $this->representation = $this->options['representation'];
        }

        if (isset($this->options['namespace'])) {
            $this->namespace = $this->options['namespace'];
        }
    }

    /**
     * @brief Sets relation attributes for this relation
     * @param type $relation
     */
    public function setRelation($relation) {

        $this->relation = $relation;

        $this->return = $this->relation['0'];
        $this->model = $this->relation['1'];
        $this->unknown = $this->relation['2'];
        $this->type = $this->relation['type'];
        if (isset($this->relation['namespace']) && !empty($this->relation['namespace'])) {
            $this->namespace = $this->relation['namespace'];
        }
        $this->foreignKey = $this->getForeignKey();
    }

    /**
     * @brief Sets relation name
     * @param type $name
     */
    public function setName($name) {

        if (!empty($name)) {
            $this->name = $name;
        }
    }

    /**
     * @brief Returns name
     * @return type
     */
    public function getName() {

        return $this->name;
    }

    /**
     * @brief Returns model
     * @return type
     */
    public function getModel() {

        return $this->model;
    }

    /**
     * @brief Returns type
     * @return type
     */
    public function getType() {

        return $this->type;
    }

    /**
     * @brief Returns representation
     * @return representation
     */
    public function getRepresentation() {

        return $this->representation;
    }

    /**
     * @brief sets namespace 
     * @param type $namespace
     */
    public function setNamespace($namespace) {
        $this->namespace = $namespace;
    }

    /**
     * @brief Returns namespace
     * @return namespace
     */
    public function getNamespace() {
        return $this->namespace;
    }

    /**
     * @brief Returns representation
     * @return build
     */
    public function getBuild() {

        return $this->build;
    }

    /**
     * @brief Returns foreign for a given model
     * @return type
     */
    public function getForeignKey() {

        if (!isset($this->relation['foreignKey']) || empty($this->relation['foreignKey'])) {

            $builder = new RelationBuilder($this);
            return $builder->fetchForeignKey($this->model);
        } else {

            return $this->relation['foreignKey'];
        }
    }

    /**
     * @brief Check if a relation is strong or not
     * @return bool
     */
    public function checkDeleteable() {

        //Checks if this relation is a strong relation
        if (!empty($this->strength))
            return 0;
        else
            return 1;
    }

    /**
     * @brief Checks representation for this relation, and sets it if exists
     * @return bool
     */
    public function checkRepresentation() {

        if (!empty($this->representation))
            return $this->representation;
        else
            return null;
    }

    /**
     * @brief Generates a specific field for this relation
     */
    public function generateField() {

        $builder = new RelationBuilder($this);

        return $builder->generateField();
    }

}
