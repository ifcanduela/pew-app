<?php

namespace app\commands;

use pew\console\Command;
use pew\console\Message;
use pew\console\CommandArguments;

class ExampleCommand extends Command
{
    /**
     * Test several features of the Console Command functionality.
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
        $this->info    ("Has a --dry-run argument?      " . ($arguments->dryRun ? "Yes" : "No")); // . PHP_EOL;
        $this->success ("Has an -f flag?                " . ($arguments->has("f") ? "Yes" : "No")); // . PHP_EOL;
        $this->warning ("What value does f have?        " . $arguments->f); // . PHP_EOL;
        $this->error   ("The first argument is          " . $arguments->at(0)); // . PHP_EOL;
        $this->warning ("The value of -f or --filter is " . $arguments->get("f", "filter")); // . PHP_EOL;
        $this->success ("The value of dryRun is         " . ($arguments->dryRun ? "true" : "false")); // . PHP_EOL;

        echo PHP_EOL . PHP_EOL;

        $this->message("<fg=black;bg=white>black</>");
        $this->message("<fg=red>red</>");
        $this->message("<fg=green>green</>");
        $this->message("<fg=yellow>yellow</>");
        $this->message("<fg=blue>blue</>");
        $this->message("<fg=magenta>magenta</>");
        $this->message("<fg=cyan>cyan</>");
        $this->message("<fg=white>white</>");
        $this->message("<fg=white>default</>");
    }
}
