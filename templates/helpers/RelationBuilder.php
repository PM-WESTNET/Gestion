<?php

namespace app\templates\helpers;

/**
 * Description of RelationBuilder
 *
 * @author smaldonado
 */
class RelationBuilder {

    const ATTRIBUTE_NAME = 'name';
    const ATTRIBUTE_TITLE = 'title';

    private $relation;

    public function __construct(Relation $relation) {

        $this->relation = $relation;
    }

    /**
     * @brief Generates a field for a specific relation
     * @return string
     */
    public function generateField() {

        $field = '';

        if (!empty($this->relation->getRepresentation())) {

            //Nombre de la funcion generadora
            $function = 'generate' . ucfirst($this->relation->getRepresentation());
            $field = $this->$function();
        }

        return $field;
    }

    /**
     * @brief Generates a dropdownList for a specific relation
     */
    protected function generateDropdownList() {

        $relationName = lcfirst($this->relation->getModel());
        $modelName = $this->relation->getModel();

        $relationAttr = $this->fetchForeignKey($modelName, $this->relation->getNamespace());
        $nameAttribute = $this->fetchNameAttribute($modelName, $this->relation->getNamespace());

        $namespacedModel = '\\' . $this->relation->getNamespace() . '\\' . $modelName;

        //Si existe el atributo, titulo y modelo, creamos el campo
        if (!empty($relationAttr) && !empty($nameAttribute) && !empty($modelName)) {

            return "\$form->field(\$model, '$relationAttr')->dropdownList(yii\helpers\ArrayHelper::map($namespacedModel::find()->all(), '$relationAttr', '$nameAttribute'),["
                    . "'encode'=>false, "
                    . "'separator'=>'<br/>',"
                    . "'prompt'=>'Select an option...'"
                    . "])";
        } else {

            return '';
        }
    }

    /**
     * @brief Generates a checkboxlist for a specific relation
     * @return string
     */
    protected function generateCheckboxList() {

        $relationName = lcfirst($this->relation->getName());
        $modelName = $this->relation->getModel();

        $relationAttr = $this->fetchForeignKey($modelName, $this->relation->getNamespace());
        $nameAttribute = $this->fetchNameAttribute($modelName, $this->relation->getNamespace());

        $namespacedModel = $this->relation->getNamespace() . '\\' . $modelName;

        if (!empty($relationAttr) && !empty($nameAttribute) && !empty($modelName)) {

            return "\$form->field(\$model, '$relationName')->checkboxList(yii\helpers\ArrayHelper::map($namespacedModel::find()->all(), '$relationAttr', '$nameAttribute'))";
        } else {

            return '';
        }
    }

    /**
     * @brief Generates a recursive checkboxlist for a specific relation
     * @return string
     */
    public function generateRecursiveCheckboxList() {

        $relationName = lcfirst($this->relation->getName());
        $ucRelationName = ucfirst($relationName);
        $modelName = $this->relation->getModel();

        $relationAttr = $this->fetchForeignKey($modelName, $this->relation->getNamespace());
        $nameAttribute = $this->fetchNameAttribute($modelName, $this->relation->getNamespace());

        $namespacedModel = $this->relation->getNamespace() . '\\' . $modelName;

        if (!empty($relationAttr) && !empty($nameAttribute) && !empty($modelName)) {

            return "\$form->field(\$model, '$relationName')->checkboxList(yii\helpers\ArrayHelper::map($namespacedModel::getOrdered$modelName(), '$relationAttr', function(\$data){  return  \$data->getIndentName();} ), ['encode'=>false, 'separator'=>'<br/>', 'class' => 'checkbox'])";
        } else {

            return '';
        }
    }

    /**
     * @brief Generates a recursive radiolist for a specific relation
     * @return string
     */
    public function generateRecursiveRadioList() {

        $relationName = lcfirst($this->relation->getName());
        $ucRelationName = ucfirst($relationName);
        $modelName = $this->relation->getModel();

        $relationAttr = $this->relation->getForeignKey();
        $primaryKey = $this->fetchPrimaryKey($modelName, $this->relation->getNamespace());
        $nameAttribute = $this->fetchNameAttribute($modelName, $this->relation->getNamespace());

        $namespacedModel = $this->relation->getNamespace() . '\\' . $modelName;

        if (!empty($relationAttr) && !empty($nameAttribute) && !empty($modelName)) {

            return "\$form->field(\$model, '$relationAttr')->radioList(yii\helpers\ArrayHelper::map($namespacedModel::getOrdered$modelName(), '$primaryKey', function(\$data){  return  \$data->getIndentName();} ), ['encode'=>false, 'separator'=>'<br/>', 'class' => 'checkbox'])";
        } else {

            return '';
        }
    }

    /**
     * @brief Fetch a foreign key from a given model related to the main model
     * @param string $relationName
     * @return string
     */
    public function fetchForeignKey($relationName, $namespace = 'app\models') {

        $relatedModel = "\\" . $namespace . "\\" . $relationName;

        $relationAttr = null;

        if (class_exists($relatedModel) && !empty($relatedModel::primaryKey())) {

            $relatedPk = $relatedModel::primaryKey();

            if (isset($relatedPk[0])) {

                $relationAttr = $relatedPk[0];
            }
        }

        return $relationAttr;
    }

    /**
     * @brief Fetch a primary key from a given model
     * @param string $modelName
     * @return string
     */
    public function fetchPrimaryKey($modelName, $namespace = 'app\models') {

        $model = "\\" . $namespace . '\\' . $modelName;

        $primaryKey = null;

        if (class_exists($model) && !empty($model::primaryKey())) {

            $pk = $model::primaryKey();

            if (isset($pk[0])) {

                $primaryKey = $pk[0];
            }
        }

        return $primaryKey;
    }

    /**
     * @brief Fetch a name or title attribute from a given model
     * @param string $relationName
     * @return string
     */
    public static function fetchNameAttribute($relationName, $namespace = 'app\models') {

        $relatedModel = "\\" . $namespace . '\\' . $relationName;

        $nameAttribute = null;

        if (class_exists($relatedModel)) {

            $model = new $relatedModel;

            $relatedAttributes = $model->attributes();

            //Si existe Name, lo usamos
            if (array_search(self::ATTRIBUTE_NAME, $relatedAttributes)) {

                $nameAttribute = self::ATTRIBUTE_NAME;

                return $nameAttribute;
            }

            //Si existe title, lo usamos
            if (array_search(self::ATTRIBUTE_TITLE, $relatedAttributes)) {

                $nameAttribute = self::ATTRIBUTE_TITLE;

                return $nameAttribute;
            }

            return null;
        } else {

            return null;
        }
    }

}
