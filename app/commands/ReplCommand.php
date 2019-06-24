<?php

namespace app\commands;

use pew\console\Command;
use pew\console\CommandArguments;

class ReplCommand extends Command
{
    /** @var string */
    public $name = "repl";

    /** @var string */
    public $description = "Start a REPL session";

    public function run(CommandArguments $arguments)
    {
        if (!class_exists(\Psy\Shell::class)) {
            $this->log("warning", "You must first install PsySh:");
            $this->info("    composer require --dev psy/psysh");
            die;
        }

        $config = new \Psy\Configuration([
            'updateCheck' => 'never'
        ]);

        $shell = new \Psy\Shell($config);
        $shell->run();
    }
}
