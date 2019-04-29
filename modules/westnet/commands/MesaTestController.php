<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 10/06/16
 * Time: 15:46
 */

namespace app\modules\westnet\commands;

use app\modules\config\models\Config;
use app\modules\ticket\models\Ticket;
use app\modules\westnet\components\MesaTicketManager;
use app\modules\westnet\mesa\components\models\Categoria;
use app\modules\westnet\mesa\components\request\CategoriaRequest;
use app\modules\westnet\mesa\components\request\TicketRequest;
use app\modules\westnet\mesa\components\request\UsuarioRequest;
use Yii;
use yii\console\Controller;
use yii\db\ActiveQuery;
use yii\helpers\Console;

class MesaTestController extends Controller
{
    /**
     *  Muestra todos los usuarios de Mesa.
     */
    public function actionUsuarioAll()
    {
        $this->stdout("Mesa - Usuario\n", Console::BOLD, Console::FG_CYAN);
        
        $api = new UsuarioRequest(Config::getValue('mesa_server_address'));
        $all = $api->findAll();
        $this->stdout(print_r($all, 1));
    }

    /**
     * Muestra un usuario de mesa identificado por el id
     * @param $id
     */
    public function actionUsuario($id)
    {
        $this->stdout("Mesa - Usuario\n", Console::BOLD, Console::FG_CYAN);

        $api = new UsuarioRequest(Config::getValue('mesa_server_address'));
        $this->stdout(print_r($api->findById($id),1));
    }

    /**
     *  Muestra todos las categorias de Mesa.
     */
    public function actionCategoriaAll()
    {
        $this->stdout("Mesa - Categoria\n", Console::BOLD, Console::FG_CYAN);

        $api = new CategoriaRequest(Config::getValue('mesa_server_address'));
        $all = $api->findAll();
        $this->stdout(print_r($all, 1));
    }

    /**
     * Muestra un usuario de mesa identificado por el id
     * @param $id
     */
    public function actionCategoria($id)
    {
        $this->stdout("Mesa - Categoria\n", Console::BOLD, Console::FG_CYAN);

        $api = new CategoriaRequest(Config::getValue('mesa_server_address'));
        $this->stdout(print_r($api->findById($id),1));
    }

    /**
     *  Muestra todos las categorias de Mesa.
     */
    public function actionTicketAll()
    {
        $this->stdout("Mesa - Categoria\n", Console::BOLD, Console::FG_CYAN);

        $api = new CategoriaRequest(Config::getValue('mesa_server_address'));
        $all = $api->findAll();
        $this->stdout(print_r($all, 1));
    }

    /**
     * Muestra un ticket de mesa identificado por el id
     * @param $id
     */
    public function actionTicket($id)
    {
        $this->stdout("Mesa - Ticket\n", Console::BOLD, Console::FG_CYAN);

        $api = new TicketRequest(Config::getValue('mesa_server_address'));
        $this->stdout(print_r($api->findById($id),1));
    }

    /**
     * Crea un ticket de mesa identificado por el id
     * @param $id
     */
    public function actionTicketCreate($autor_id, $asignado_id, $categoria_id, $descripcion, $contrato_id, $customer_code)
    {
        $this->stdout("Mesa - Ticket\n", Console::BOLD, Console::FG_CYAN);

        $api = new TicketRequest(Config::getValue('mesa_server_address'));
        $this->stdout(print_r($api->create($autor_id, $asignado_id, $categoria_id, $descripcion, $contrato_id, $customer_code),1));
    }

    /**
     * Actualiza un ticket de mesa identificado por el id
     * @param $id
     */
    public function actionTicketUpdate($id, $estado, $descripcion, $autor_id)
    {
        $this->stdout("Mesa - Ticket\n", Console::BOLD, Console::FG_CYAN);

        $api = new TicketRequest(Config::getValue('mesa_server_address'));
        $this->stdout(print_r($api->update($id, $estado, $descripcion, $autor_id, new \DateTime('2016-01-01 14:00:00')),1));
    }

    /**
     * Actualiza un ticket de mesa identificado por el id
     * @param $id
     */
    public function actionTicketEstados()
    {
        $this->stdout("Mesa - Ticket\n", Console::BOLD, Console::FG_CYAN);

        $api = new TicketRequest(Config::getValue('mesa_server_address'));
        $this->stdout(print_r($api->findEstados(),1));
    }

    /**
     * Actualizo el historial de observaciones del ticket en mesa con los de gestion.
     * @param $id
     */
    public function actionUpdateRemoteTicket($id)
    {
        $this->stdout("Mesa - Ticket\n", Console::BOLD, Console::FG_CYAN);
        $inst = MesaTicketManager::getInstance();

        $inst->updateRemoteTicket(Ticket::findOne(['ticket_id'=>$id]));
    }

    /**
     * Actualizo el historial de observaciones del ticket en mesa con los de gestion.
     * @param $id
     */
    public function actionUpdateLocalTicket($id)
    {
        $this->stdout("Mesa - Ticket\n", Console::BOLD, Console::FG_CYAN);
        $inst = MesaTicketManager::getInstance();

        $inst->updateLocalTicket(Ticket::findOne(['ticket_id'=>$id]));
    }
}