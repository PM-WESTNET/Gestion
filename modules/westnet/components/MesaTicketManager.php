<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 17/10/16
 * Time: 13:38
 */

namespace app\modules\westnet\components;

use app\modules\config\models\Config;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\ticket\models\History;
use app\modules\ticket\models\Observation;
use app\modules\ticket\models\Status;
use app\modules\ticket\TicketModule;
use app\modules\westnet\mesa\components\models\HistoricoTicket;
use app\modules\westnet\mesa\components\models\Ticket as TicketMesa;
use app\modules\ticket\models\Ticket;
use app\modules\westnet\mesa\components\request\TicketRequest;
use Yii;

/**
 * Class MesaTicketManager
 * Clase para el manejo de tickets contra mesa y vise versa.
 * @package app\modules\westnet\components
 */
class MesaTicketManager
{
    private static $instance = null;

    /**
     * Retorno el singleton del manager.
     * @return MesaTicketManager|null
     */
    public static function getInstance()
    {
        if(!self::$instance) {
            self::$instance = new MesaTicketManager();
        }

        return self::$instance;
    }

    /**
     * Actualizo el ticket de mesa con el ticket de gestion.
     *
     * @param Ticket $ticket
     */
    public function updateRemoteTicket(Ticket $ticket)
    {
        $external_id = null;
        $ticketRequest = new TicketRequest(Config::getValue('mesa_server_address'));

        // Para cada asignacion, busco el ticket original
        foreach( $ticket->assignations as $assignation) {
            if($assignation->external_id != $external_id) {
                $ticketOriginal = $ticketRequest->findById($assignation->external_id);

                if($ticketOriginal) {
                    // Si hay diferencia en la cantidad de observaciones, creo las ultimas
                    $historial = $ticketOriginal->historial;
                    foreach($ticket->observations as $observation) {
                        if(!$this->existObservation($observation, $historial)) {
                            $ticketRequest->update(
                                $assignation->external_id,
                                $ticket->status->name,
                                (Yii::$app instanceof \yii\console\Application ? 'Console' : Yii::$app->user->username) . " - " .$observation->description,
                                $ticket->category->external_user_id,
                                new \DateTime($observation->date . " " .  $observation->time)
                            );
                        }
                    }
                }

            }

            $external_id = $assignation->external_id;
        }
    }

    /**
     * Actualizo el ticket de gestion con el ticket de mesa.
     * @param Ticket $ticket
     */
    public function updateLocalTicket(Ticket $ticket)
    {
        $external_id = null;
        $negative = false;
        $ticketRequest = new TicketRequest(Config::getValue('mesa_server_address'));

        $mesa_negative_survey_id = Config::getValue('mesa_negative_survey_id');
        $observations = $ticket->observations;
        $mesa_negative_survey = false;
        foreach( $ticket->assignations as $assignation) {
            if($assignation->external_id != $external_id) {
                $ticketOriginal = $ticketRequest->findById($assignation->external_id);
                if($ticketOriginal) {
                    // Si hay diferencia en la cantidad de observaciones, creo las ultimas
                    /** @var HistoricoTicket $historial */
                    $historial = $ticketOriginal->historial;
                    if($historial) {
                        foreach($ticketOriginal->historial as $historial) {

                            if(!$this->existHistory($historial, $observations)) {
                                $obs = new Observation();
                                $obs->ticket_id = $ticket->ticket_id;
                                $obs->user_id = (Yii::$app instanceof \yii\console\Application ? 1 : Yii::$app->user->id);
                                $obs->title = Yii::t('ticket', 'Observacion de Mesa.');
                                $obs->description = $historial->descripcion;
                                $obs->date = $historial->fecha_actualizacion->format('d-m-Y');
                                $obs->time = $historial->fecha_actualizacion->format('H:i:s');
                                $obs->datetime = (new \DateTime('now'))->format('Y-m-d H:i:s');
                                $obs->save();
                                $observations[] = $obs;
                            }

                            if($historial->estado != $ticket->status->name) {
                                $status = Status::findOne(['name'=>$historial->estado]);
                                $ticket->status_id = $status->status_id;
                                $update[] = 'status_id';
                                if (!$status->is_open){
                                    echo "Close: " . $ticket->ticket_id."\n";
                                    $ticket->finish_date = $historial->fecha_actualizacion->format('Y-m-d H:i:s');
                                    $update[] = 'finish_date';
                                }
                                $ticket->updateAttributes($update);
                            }
                            if($historial->categoria->id == $mesa_negative_survey_id) {
                                $negative = true;
                                $contract = $ticket->contract;
                                if($contract) {
                                    $contract->updateAttributes(['status' => Contract::STATUS_NEGATIVE_SURVEY]);
                                }
                            }


                        }
                    }
                }

            }

            $external_id = $assignation->external_id;
        }
        if($negative) {
            $ticket->updateAttributes(['category_id' => $mesa_negative_survey_id]);
        }
    }

    /**
     * Retorno true o false en caso de que exista la observacion en el historico de mesa.
     * @param Observation $observation
     * @param $historial
     * @return bool
     */
    private function existObservation(Observation $observation, $historial)
    {
        $observationDateTime = new \DateTime($observation->date . " " .  $observation->time);
        /** @var HistoricoTicket $value */
        foreach ($historial as $value) {
            if( $value->fecha_actualizacion->format('Y-m-d H:i:s') == $observationDateTime ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retorno true o false en caso de que exista el historico en las observaciones de gestion.
     * @param Observation $observation
     * @param $historial
     * @return bool
     */
    private function existHistory(HistoricoTicket $historicoTicket, $observations)
    {
        /** @var Observation $value */
        foreach ($observations as $value) {
            if( $historicoTicket->fecha_actualizacion->format('Y-m-d H:i:s') == (new \DateTime($value->date . " " .  $value->time))->format('Y-m-d H:i:s') ) {
                return true;
            }
        }
        return false;
    }

}