<?php

namespace app\commands;

use pew\console\Command ;
use pew\console\CommandArguments;

class ExampleCommand extends Command
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
        return 'example';
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
        return 'Test several features of the Console Command functionality';
    }

    /**
     * Example command.
     *
     * Run this command by typing `php run example --dry-run --filter 1` in the 
     * root folder of your app, where the "run"  script is found.
     *
     * @param CommandArguments $arguments Command-line arguments
     * @return null
     */
    public function run(CommandArguments $arguments)
    {
        var_dump($arguments->dryRun);
        var_dump($arguments->has('f'));
        var_dump($arguments->f);
        var_dump($arguments->at(0));
        var_dump($arguments->get('f', 'filter'));
    }
}
