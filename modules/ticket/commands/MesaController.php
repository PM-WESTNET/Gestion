<?php

/** 
 * To execute this command, use ./yii ticket/expiration/check from bash
 */

namespace app\modules\ticket\commands;

use app\modules\ticket\models\Category;
use app\modules\ticket\models\search\TicketSearch;
use app\modules\ticket\models\Status;
use app\modules\westnet\components\MesaTicketManager;
use app\modules\westnet\mesa\components\models\Categoria;
use app\modules\westnet\mesa\components\request\CategoriaRequest;
use app\modules\westnet\mesa\components\request\TicketRequest;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use app\modules\ticket\models\Ticket;
use app\modules\config\models\Config;
use app\modules\ticket\TicketModule;

class MesaController extends Controller {

    /**
     * This command Create the category from mesa
     * @param string $message the message to be echoed.
     */
    public function actionCreateCategorias() {

        //Intro
        $this->stdout('Arya[Ticket]', Console::BOLD, Console::FG_CYAN);
        $this->stdout(" | Command to retrieve categories from mesa \n\n", Console::BOLD);
        
        echo "Starting... \n";

        $request = new CategoriaRequest(Config::getValue('mesa_server_address'));
        $categories = $request->findAll();
        
        if (!empty($categories)) {
            Yii::$app->dbticket->createCommand("SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;")->execute();

            /** @var Categoria $category */
            foreach ($categories as $category) {
                // Busco si existe y despues actualizo
                $cat = Category::findOne(['category_id'=>$category->id]);
                if($cat) {
                    $cat->category_id   = $category->id;
                    $cat->parent_id     = $category->padre_id;
                    $cat->name          = $category->nombre;
                    $cat->slug          = $category->nombre;
                } else {
                    $cat = new Category();
                    $cat->category_id   = $category->id;
                    $cat->parent_id     = $category->padre_id;
                    $cat->name          = $category->nombre;
                    $cat->slug          = $category->nombre;
                }
                $cat->save(false);
            }
            Yii::$app->dbticket->createCommand("SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;")->execute();
            $this->updateTree(0);
            $this->stdout("\nProccess successfully finished. \n", Console::BOLD, Console::FG_GREEN);
        }else {
            $this->stdout("\nNo open tickets found. Finishing process. \n", Console::BOLD, Console::FG_RED);
        }
    }

    /**
     * Update category tree.
     *
     * @param string $message the message to be echoed.
     */
    public function actionUpdateTree()
    {
        $this->updateTree(0);
    }

    /**
     * Actualiza el arbol de categorias.
     *
     * @param int $parent_id
     * @param int $left
     * @return int
     * @internal param int|null $parent_account_id
     */
    private function updateTree($parent_id=0, $left = 0)
    {
        $right = $left +1;
        $query = Category::find()
            ->where(['parent_id'=>$parent_id]);
        if($parent_id==0) {
            $query->orWhere(['parent_id'=>null]);
        }
        $categories = $query->all();

        foreach($categories as $category) {
            $right = $this->updateTree($category->category_id, $right);
        }
        Yii::$app->dbticket->createCommand('update category set lft=' . $left . ", rgt=".$right . " WHERE category_id=".$parent_id)->execute();

        return $right +1;
    }

    /**
     * Crea los estados en base a los existentes en mesa.
     *
     */
    public function actionCreateEstados()
    {
        //Intro
        $this->stdout('Arya[Ticket]', Console::BOLD, Console::FG_CYAN);
        $this->stdout(" | Command to retrieve estados from mesa \n\n", Console::BOLD);

        echo "Starting... \n";

        $request = new TicketRequest(Config::getValue('mesa_server_address'));
        $estados = $request->findEstados();

        if (!empty($estados)) {
            Yii::$app->dbticket->createCommand("SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;")->execute();

            foreach ($estados as $key=>$estado) {
                $st = new Status();
                $st->name = $estado;
                $st->save(false);
            }
            Yii::$app->dbticket->createCommand("SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;")->execute();
            $this->stdout("\nProccess successfully finished. \n", Console::BOLD, Console::FG_GREEN);
        }else {
            $this->stdout("\nNo open tickets found. Finishing process. \n", Console::BOLD, Console::FG_RED);
        }
    }

    public function actionUpdateLocalTickets()
    {
        $this->stdout('Arya[Ticket]', Console::BOLD, Console::FG_CYAN);
        $this->stdout(" | Update local tickets. \n\n", Console::BOLD);

        $tickets = (new TicketSearch())->searchExternalTicket();

        $manager = MesaTicketManager::getInstance();

        foreach($tickets as $ticket) {
            $manager->updateLocalTicket($ticket);
        }

    }
}