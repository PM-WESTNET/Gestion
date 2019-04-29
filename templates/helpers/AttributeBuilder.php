<?php

namespace app\templates\helpers;

/**
 * Description of AttributeBuilder
 *
 * @author smaldonado
 */
class AttributeBuilder {

    private $column;

    public function __construct(\yii\db\ColumnSchema $column) {

        $this->column = $column;
    }

    /**
     * @brief Builds a date input with functional jQuery/DatePicker
     * @param type $attribute
     * @return type
     */
    public function buildDate($attribute) {

        //Gets app language
        $lang = $this->retrieveLanguage();

        return "\$form->field(\$model, '$attribute')->widget(\yii\jui\DatePicker::classname(), ["
                . "'language' => '$lang',"
                . "'dateFormat' => 'dd-MM-yyyy',"
                . "'options' => ["
                . "'class' => 'form-control',"
                . "],"
                . "])";
    }

    /**
     * Retrieves app language
     * @return string
     */
    private function retrieveLanguage() {

        if (isset(\Yii::$app->params['language'])) {
            return \Yii::$app->params['language'];
        } else {
            return 'es-AR';
        }
    }

}
