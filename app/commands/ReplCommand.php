<?php

namespace app\commands;

use pew\console\Command;
use pew\console\CommandArguments;

class ReplCommand extends Command
{
    /**
     * Command name.
     *
     * This is the name used to invoke the command in the console.
     *
     * @return string
     */
    public function name(): string
    {
        return "repl";
    }

    /**
     * Command description.
     *
     * Use this value to provide a brief explanation of the command.
     *
     * @return string
     */
    public function description(): string
    {
        return "Start a REPL session";
    }

    public function run(CommandArguments $arguments)
    {
        if (!class_exists(\Psy\Shell::class)) {
            echo $this->infoBox("You must first install PsySh: composer require --dev psy/psysh");
            die;
        }

        $config = new \Psy\Configuration([
            'updateCheck' => 'never'
        ]);

        if ($arguments->initDb) {
            var_dump("INIT DB");
            \pew\model\TableManager::instance(pew("tableManager"));
        }

        $shell = new \Psy\Shell($config);
        $shell->run();
    }
}
