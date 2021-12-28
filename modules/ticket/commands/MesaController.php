<?php

/** 
 * To execute this command, use ./yii ticket/expiration/check from bash
 */

namespace app\modules\ticket\commands;

use app\modules\ticket\models\Category;
use app\modules\ticket\models\search\TicketSearch;
use app\modules\ticket\models\Status;
use app\modules\ticket\models\StatusHasAction;
use app\modules\westnet\components\MesaTicketManager;
use app\modules\westnet\mesa\components\models\Categoria;
use app\modules\westnet\mesa\components\request\CategoriaRequest;
use app\modules\westnet\mesa\components\request\TicketRequest;
use Yii;
use yii\console\Controller;
use yii\db\Query;
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
     * El comando verifica que la categoria de mesa, tenga el mismo id en gestion,
     * en caso de no ser la misma categoria realmente, reemplaza los valores y crea la categoria que se estaria reemplazando
     * @param string $message the message to be echoed.
     */
    public function actionUpdateCategory($mesa_category_id)
    {
        $this->stdout('Arya[Ticket]', Console::BOLD, Console::FG_CYAN);
        $this->stdout(" | Command to verify a category from mesa \n\n", Console::BOLD);

        echo "Starting... \n";

        $request = new CategoriaRequest(Config::getValue('mesa_server_address'));
        $category = $request->findById($mesa_category_id);

        if (!empty($category)) {

                // Busco si existe
                $cat = Category::findOne(['category_id' => $category->id]);

                if($cat) {
                    if($cat->slug != $category->nombre) {
                        $old_name = $cat->name;
                        $old_slug = $cat->slug;
                        $old_parent_id = $cat->parent_id;
                        $old_id = $cat->category_id;
                        $old_notify = $cat->notify;
                        $old_description = $cat->description;

                        $cat->name = $category->nombre;
                        $cat->slug = $category->nombre;
                        $cat->parent_id = $category->padre_id;

                        $old_new_category = new Category([
                            'name' => $old_name,
                            'slug' => $old_slug,
                            'parent_id' => $old_parent_id,
                            'notify' => $old_notify,
                            'description' => $old_description
                        ]);
                        $old_new_category->save();

                        echo "Categoria vieja: $old_new_category->category_id - $old_new_category->name\n";
                        echo "Categoria nueva: $mesa_category_id - $cat->name\n";

                        echo "Cambiando categoria de tickets...\n";
                        Ticket::updateAll(['category_id' => $old_new_category->category_id], ['category_id' => $old_id]);

                        echo "Cambiando categoria de StatusHasAction...\n";
                        StatusHasAction::updateAll(['ticket_category_id' => $old_new_category->category_id], ['ticket_category_id' => $old_id]);

                        echo "Cambiando padres de categorias con id viejo...\n";
                        Category::updateAll(['parent_id' => $old_new_category->category_id], ['parent_id' => $old_id]);

                        echo "Cambiando categoria de configuraciones donde se incluya el id viejo...\n";
                        if(Config::getValue('credit-bill-category-id') == $old_id) {
                            Config::setValue('credit-bill-category-id', $old_new_category->category_id);
                        }

                        if(Config::getValue('bill-category-id') == $old_id) {
                            Config::setValue('bill-category-id', $old_new_category->category_id);
                        }

                        if(Config::getValue('cobranza_category_id') == $old_id) {
                            Config::setValue('cobranza_category_id', $old_new_category->category_id);
                        }

                        if(Config::getValue('installations_category_id') == $old_id) {
                            Config::setValue('installations_category_id', $old_new_category->category_id);
                        }

                        if(Config::getValue('baja-category-id') == $old_id) {
                            Config::setValue('baja-category-id', $old_new_category->category_id);
                        }

                        if(Config::getValue('ticket-category-edicion-de-datos-id') == $old_id) {
                            Config::setValue('ticket-category-edicion-de-datos-id', $old_new_category->category_id);
                        }

                        if(Config::getValue('instalation_category_id') == $old_id) {
                            Config::setValue('instalation_category_id', $old_new_category->category_id);
                        }

                        if(Config::getValue('mesa_category_low_reason') == $old_id) {
                            Config::setValue('mesa_category_low_reason', $old_new_category->category_id);
                        }

                        if(Config::getValue('fibra_instalation_category_id') == $old_id) {
                            Config::setValue('fibra_instalation_category_id', $old_new_category->category_id);
                        }
                    } else {
                        echo "Las categorias coinciden.\n";
                    }

                } else {
                    echo "La categoria no existe, va a ser creada.\n";
                    $cat = new Category();
                    $cat->category_id   = $category->id;
                    $cat->parent_id     = $category->padre_id;
                    $cat->name          = $category->nombre;
                    $cat->slug          = $category->nombre;
                }

                $cat->save(false);
            }
            $this->updateTree(0);
            $this->stdout("\nProccess successfully finished. \n", Console::BOLD, Console::FG_GREEN);
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
