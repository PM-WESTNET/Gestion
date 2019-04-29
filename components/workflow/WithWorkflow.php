<?php

namespace app\components\workflow;

/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 18/09/15
 * Time: 12:47
 */


trait WithWorkflow {
    /**
     * Retorna el atributo que maneja el estado del objeto para el workflow.
     *
     * @return mixed
     */
    public abstract function getWorkflowAttr();

    /**
     * Retorna los estados.
     *
     * @return mixed
     */
    public abstract function getWorkflowStates();

    /**
     * Se implementa en el caso que se quiera crear un log de estados.
     * @return mixed
     */
    public abstract function getWorkflowCreateLog();

    /**
     * Retorna los posibles estados en base al estado actual.
     *
     * @return string
     */
    public function getWorkflowPossibleStates()
    {
        $states = $this->getWorkflowStates();
        $field = $this->getWorkflowAttr();
        if (array_key_exists($this->$field, $states)) {
            return $states[$this->$field];
        } else {
            return "";
        }
    }

    /**
     * Retorna verdadero o falso si el modelo puede cambiar al estado pasado como argumento.
     *
     * @param $new_state
     * @return bool
     */
    public function can($new_state)
    {
        $possibles = $this->getWorkflowPossibleStates();
        if (is_array($possibles)) {
            return array_search($new_state, $possibles) !== false;
        } else {
            return ($new_state == $possibles);
        }
    }

    /**
     * Cambia el estado del modelo al pasado como parametro.
     *
     * @param $new_state
     * @return bool
     */
    public function changeState ($new_state)
    {
        if ($this->can($new_state)) {
            $field = $this->getWorkflowAttr();
            $this->getWorkflowCreateLog();
            $this->$field = $new_state;
            if ( $this->validate() ) {
                return $this->save();
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
