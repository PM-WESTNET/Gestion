<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 19/06/15
 * Time: 14:21
 */

namespace app\modules\invoice\components\einvoice;

/**
 * Class ApiBase
 * Implementacion base de ApiInterface
 *
 * @package app\modules\invoice\components\api
 */
abstract class ApiBase implements ApiInterface
{
    protected $errors;
    protected $observations;
    protected $events;

    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors()
    {
        return !empty($this->errors);
    }

    public function getObservations()
    {
        return $this->observations;
    }

    public function hasObservations()
    {
        return !empty($this->observations);
    }

    public function getEvents()
    {
        return $this->events;
    }

    public function hasEvents()
    {
        return !empty($this->events);
    }
}