<?php

namespace app\services\debugbar;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

class PewVersionCollector extends DataCollector implements Renderable
{
    public function getName()
    {
        return "pew-version";
    }

    public function collect()
    {
        $composerLock = json_decode(file_get_contents(root("composer.lock")));

        foreach ($composerLock->packages as $package) {
            if ($package->name === "ifcanduela/pew") {
                return $package->version;
            }
        }

        return null;
    }

    public function getWidgets()
    {
        return [
            "pew-version" => [
                "icon" => "wheelchair-alt",
                "tooltip" => "Pew version",
                "map" => "pew-version",
                "default" => "null"
            ]
        ];
    }
}
