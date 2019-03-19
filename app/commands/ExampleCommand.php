<?php

namespace app\commands;

use pew\console\Command;
use pew\console\Message;
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
        return "example";
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
        return "Test several features of the Console Command functionality";
    }

    /**
     * Default command parameters.
     *
     * The keys in the array must be the camel-case version of the command-line
     * arguments.
     *
     * @return array
     */
    public function getDefaultArguments()
    {
        return [
            "f" => "default filter here",
            "filter" => "default filter here",
            "dryRun" => false,
        ];
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
        echo $this->info    ("Has a --dry-run argument?      " . ($arguments->dryRun ? "Yes" : "No")); // . PHP_EOL;
        echo $this->success ("Has an -f flag?                " . ($arguments->has("f") ? "Yes" : "No")); // . PHP_EOL;
        echo $this->warning ("What value does f have?        " . $arguments->f); // . PHP_EOL;
        echo $this->error   ("The first argument is          " . $arguments->at(0)); // . PHP_EOL;
        echo $this->warning ("The value of -f or --filter is " . $arguments->get("f", "filter")); // . PHP_EOL;
        echo $this->success ("The value of dryRun is         " . ($arguments->dryRun ? "true" : "false")); // . PHP_EOL;

        echo PHP_EOL . PHP_EOL;

        echo $this->message("black")->fg(Message::COLOR_BLACK)->bg(Message::COLOR_WHITE);
        echo $this->message("red")->fg(Message::COLOR_RED);
        echo $this->message("green")->fg(Message::COLOR_GREEN);
        echo $this->message("yellow")->fg(Message::COLOR_YELLOW);
        echo $this->message("blue")->fg(Message::COLOR_BLUE);
        echo $this->message("magenta")->fg(Message::COLOR_MAGENTA);
        echo $this->message("cyan")->fg(Message::COLOR_CYAN);
        echo $this->message("white")->fg(Message::COLOR_WHITE);
        echo $this->message("default")->fg(Message::COLOR_DEFAULT);
    }
}
