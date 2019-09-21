<?php

namespace app\services\debugbar;

use DebugBar\Bridge\Twig\TwigCollector;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

class TemplateCollector extends TwigCollector
{
    protected $templates = [];

    /**
     * Create a TemplateCollector
     */
    public function __construct()
    {
    }

    public function getName()
    {
        return "templates";
    }

    public function getWidgets()
    {
        return [
             "templates" => [
                "icon" => "leaf",
                "widget" => "PhpDebugBar.Widgets.TemplatesWidget",
                "map" => "templates",
                "default" => json_encode(["templates" => []]),
            ],
            "templates:badge" => [
                "map" => "templates.nb_templates",
                "default" => 0
            ]
        ];
    }

    public function addTemplate($path, $params)
    {
        $path = str_replace(root(), "", $path);

        $template = [
            "name" => $path,
            "param_count" => count($params),
            "params" => $params,
            "type" => "php",
        ];

        $this->templates[] = $template;
    }

    public function collect()
    {
        return [
            "nb_templates" => count($this->templates),
            "templates" => $this->templates,
        ];
    }
}
