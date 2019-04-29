<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 19/06/15
 * Time: 14:21
 */

namespace app\modules\invoice\components\einvoice;

/**
 * Interface ApiInterface
 * @package app\modules\invoice\components\api
 */
interface ApiInterface
{
    /**
     * Returns the version of the API.
     *
     * @return mixed
     */
    public function getVersion();

    /**
     * Return an arrray with the errors.
     *
     * @return mixed
     */
    public function getErrors();

    /**
     * Returns true or false if the API has errors.
     *
     * @return boolean
     */
    public function hasErrors();

    /**
     * Return an arrray with the observations.
     *
     * @return mixed
     */
    public function getObservations();

    /**
     * Returns true or false if the API has observations.
     *
     * @return mixed
     */
    public function hasObservations();

    /**
     * Return an arrray with the events.
     *
     * @return mixed
     */
    public function getEvents();

    /**
     * Returns true or false if the API has events.
     *
     * @return mixed
     */
    public function hasEvents();

    /**
     * Run the creation of the invoice.
     *
     * @param $object
     * @return mixed
     */
    public function run($object);

    /**
     * Returns true or false if the service is availeable.
     *
     * @return boolean
     */
    public function serviceAvailable();

    /**
     * Returns the result
     *
     * @return mixed
     */
    public function getResult();
}