<?php

/** 
 * To execute this command, use ./yii ticket/expiration/check from bash
 */

namespace app\modules\ticket\commands;

use yii\console\Controller;
use yii\helpers\Console;
use app\modules\ticket\models\Ticket;
use app\modules\config\models\Config;
use app\modules\ticket\TicketModule;

class ExpirationController extends Controller {

    /**
     * This command checks tasks and sets them as "expired" if needed
     * @param string $message the message to be echoed.
     */
    public function actionCheck() {

        //Intro
        $this->stdout('Arya[Ticket]', Console::BOLD, Console::FG_CYAN);
        $this->stdout(" | Command to check overdue tickets \n\n", Console::BOLD);
        
        echo "Starting... \n";
        
        //Find limit for open tickets being open
        //$openThreshold = (!empty(Config::getConfig('expiration_timeout')->value)) ? Config::getConfig('expiration_timeout')->value : \Yii::$app->getModule('ticket')->params['expiration_timeout'];
        $daysLimit = \Yii::$app->getModule('ticket')->params['expiration_timeout'];        
        $dateThreshold = new \DateTime(date("Y-m-d"));
        $dateThreshold->modify("-$daysLimit day");
        $dateLimit = $dateThreshold->format("Y-m-d");
                        
        //Finding open tickets within limit
        $openTickets = Ticket::find()->isStatusOpen()->andWhere(['<=', 'start_date', $dateLimit])->all();
        
        //Ticket count
        $this->stdout(count($openTickets), Console::BOLD, Console::FG_BLUE);
        echo " open tickets found \n\n";

        //Tasks verification
        if (!empty($openTickets)) {
            foreach ($openTickets as $ticket) {
                $customer = $ticket->customer->name;
                $this->stdout("Processing ", Console::FG_GREY);
                $this->stdout("$ticket->title de $customer...", Console::BOLD, Console::FG_GREY);
                if($ticket->closeTicket())
                    $this->stdout("    Done!. \n\n", Console::FG_GREY, Console::FG_GREEN);
                else
                    $this->stdout("    Error!. \n\n", Console::FG_GREY, Console::FG_RED);
            }
            $this->stdout("\nProccess successfully finished. \n", Console::BOLD, Console::FG_GREEN);
        }else {
            $this->stdout("\nNo open tickets found. Finishing process. \n", Console::BOLD, Console::FG_RED);
        }
    }

}
