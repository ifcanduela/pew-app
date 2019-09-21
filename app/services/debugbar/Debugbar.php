<?php

namespace app\services\debugbar;

use DebugBar\StandardDebugBar;
use DebugBar\JavascriptRenderer;
use DebugBar\DataCollector\DataCollector;

use ifcanduela\events\CanListenToEvents;

/**
 * A wrapper around maximebf/debugbar.
 */
class Debugbar
{
    use CanListenToEvents;

    /** @var StandardDebugBar */
    private $debugbar;

    /** @var JavascriptRenderer */
    private $debugbarRenderer;

    /** @var boolean */
    private $enabled = false;

    /** @var object */
    private $dummyCollector;

    /**
     * Create a Debugbar manager.
     *
     * @param boolean $enabled
     */
    public function __construct(bool $enabled = false)
    {
        $this->enabled = $enabled;

        $this->dummyCollector = new class {
            public function __call($method, $arguments) {}
        };

        if ($this->enabled) {
            $this->debugbar = new StandardDebugBar();

            $this->debugbar->addCollector(new TemplateCollector());
            $this->debugbar->addCollector(new ActionCollector());
            $this->debugbar->addCollector(new DatabaseCollector(pew("db")));

            $this->listento("view.render", function ($payload) {
                $this->addTemplate(...$payload);
            });

            $this->listenTo("timer.start", function ($payload) {
                $this->startMeasure(...$payload);
            });

            $this->listenTo("timer.stop", function ($payload) {
                $this->stopMeasure(...$payload);
            });
        }
    }

    public function sendDataInHeaders()
    {
        $this->debugbar->sendDataInHeaders();
    }

    /**
     * Get a collector by name.
     *
     * @param string $name
     * @return DataCollector
     */
    public function getCollector(string $name)
    {
        if ($this->enabled) {
            return $this->debugbar[$name];
        }

        return $this->dummyCollector;
    }

    /**
     * Render the asset tags for the <head>.
     *
     * @return string|null
     */
    public function renderHead()
    {
        if ($this->enabled) {
            return $this->getRenderer()->renderHead();
        }
    }

    /**
     * Render the asset tags for the <body>.
     *
     * @return string|null
     */
    public function render()
    {
        if ($this->enabled) {
            return $this->getRenderer()->render();
        }
    }

    /**
     * Get the widget renderer.
     *
     * @return JavascriptRenderer
     */
    protected function getRenderer()
    {
        if (!$this->debugbarRenderer) {
            $this->debugbarRenderer = $this->debugbar->getJavascriptRenderer(url("debugbar"));
        }

        return $this->debugbarRenderer;
    }

    /**
     * Add a message to the list.
     *
     * @param string $message
     * @param string $label
     */
    public function addComment(string $message, $label = "info")
    {
        $this->getCollector("messages")->addMessage($message, $label);
    }

    /**
     * Add a rendered template to the list.
     *
     * @param string $path
     * @param array $params
     */
    public function addTemplate(string $path, $params)
    {
        $this->getCollector("templates")->addTemplate($path, $params);
    }

    /**
     * Open a timer.
     *
     * @param string $key
     * @param string $label
     */
    public function startMeasure(string $key, string $label)
    {
        $this->getCollector("time")->startMeasure($key, $label);
    }

    /**
     * Close a timer.
     *
     * @param string $key
     */
    public function stopMeasure($key)
    {
        $this->getCollector("time")->stopMeasure($key);
    }
}
