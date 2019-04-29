<?php

/**
 * To execute this command, use ./yii agenda/expiration/check from bash
 */

namespace app\modules\agenda\commands;

use yii\console\Controller;
use yii\helpers\Console;
use app\modules\agenda\models\Task;

class ExpirationController extends Controller {

    /**
     * This command checks tasks and sets them as "expired" if needed
     * @param string $message the message to be echoed.
     */
    public function actionCheck() {

        //Intro
        $this->stdout('Arya[Agenda]', Console::BOLD, Console::FG_CYAN);
        $this->stdout(" | Command to check expired/overdue tasks and notify to responsible users  \n\n", Console::BOLD);
        echo "Starting... \n";

        //Finding users
        $users = \webvimark\modules\UserManagement\models\User::findAll([
                    'status' => \webvimark\modules\UserManagement\models\User::STATUS_ACTIVE
        ]);

        //User count
        $this->stdout(count($users), Console::BOLD, Console::FG_BLUE);
        echo " active users found \n\n";

        //Tasks verification
        if (!empty($users)) {
            foreach ($users as $user) {
                $this->stdout("Processing ", Console::FG_GREY);
                $this->stdout("$user->username", Console::BOLD, Console::FG_GREY);
                $this->stdout(" tasks... \n", Console::FG_GREY);
                $taskCount = Task::markAllAsExpired($user->id);
                if ($taskCount > 0)
                    $this->stdout("    $taskCount expired task/s found and labeled for this user. \n\n", Console::FG_GREY, Console::FG_YELLOW);
                else
                    $this->stdout("    No expired tasks found for this user. \n\n", Console::FG_GREY, Console::FG_GREEN);
            }
            $this->stdout("\nProccess successfully finished. \n", Console::BOLD, Console::FG_GREEN);
        }else {
            $this->stdout("\nNo active users found. Finishing process", Console::BOLD, Console::FG_RED);
        }
    }

}
