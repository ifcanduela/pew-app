<?php

namespace app\commands;

use pew\console\Command as ConsoleCommand;
use pew\console\CommandArguments as ConsoleCommandArguments;

class ExampleCommand extends ConsoleCommand
{
    public function name()
    {
        return 'example';
    }

    public function description()
    {
        return 'Test several features of the ConsoleCommand functionality';
    }

    /**
     * Example command.
     *
     * Run this command by typing `php run example --filter 1` in the root folder of 
     * your app, where the "run"  script is found.
     * 
     * @param  ConsoleCommandArguments $arguments Command-line arguments
     * @return null
     */
    public function run(ConsoleCommandArguments $arguments)
    {
        var_dump($arguments->dryRun);
        var_dump($arguments->has('f'));
        var_dump($arguments->f);
        var_dump($arguments->at(0));
        var_dump($arguments->get('f', 'filter'));
    }
}
