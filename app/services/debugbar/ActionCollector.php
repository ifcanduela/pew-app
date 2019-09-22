<?php

namespace app\services\debugbar;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

class ActionCollector extends DataCollector implements Renderable
{
    public function getName()
    {
        return "action";
    }

    public function collect()
    {
        $method = pew("request")->getMethod();

        try {
            return "$method " . pew("controller_slug") . "/" . pew("action_slug");
        } catch (\Exception $e) {
            return "{$method} anonymous function";
        }
    }

    public function getWidgets()
    {
        return [
            "mycollector" => [
                "icon" => "cog",
                "tooltip" => "Controller and action",
                "map" => "action",
                "default" => "'Anonymous function'"
            ]
        ];
    }
}
