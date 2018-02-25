<?php

namespace app\commands;

use pew\console\Command;
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
     * Run this command by typing `php run example` in the root folder of your app, where the "run"
     * script is found. Try also with different arguments:
     *
     * php run example pew -f 99
     * php run example pew --dry-run --filter
     *
     * @param CommandArguments $arguments Command-line arguments
     * @return null
     */
    public function run(CommandArguments $arguments)
    {
        echo 'Has a --dry-run argument?       ' . ($arguments->dryRun ? 'Yes' : 'No') . PHP_EOL;
        echo 'Has an -f flag?                 ' . ($arguments->has('f') ? 'Yes' : 'No') . PHP_EOL;
        echo 'What value does f have?         ' . $arguments->f . PHP_EOL;
        echo 'The first argument is           ' . $arguments->at(0) . PHP_EOL;
        echo 'The value of -f or --filter is  ' . $arguments->get('f', 'filter') . PHP_EOL;
    }
}
