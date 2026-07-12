<?php

namespace Jeffreyvr\WPPluginUpdater;

use Closure;

class WPPluginUpdater
{
    public bool|Closure $canCheck = false;

    public array $actions = [
        'activate-license' => null,
        'deactivate-license' => null,
        'check-license' => null,
        'check-update' => null,
    ];

    public function __construct(
        public string $pluginFile,
        public string $pluginId,
        public string $pluginSlug,
        public string $version,
    ) {
    }

    public function setAction($name, $action)
    {
        $this->actions[$name] = $action;

        return $this;
    }

    public function setActions($actions)
    {
        $this->actions = $actions;

        return $this;
    }

    public function canCheck(): bool
    {
        if (is_callable($this->canCheck)) {
            $callback = $this->canCheck;

            return $callback();
        }

        return $this->canCheck;
    }

    public function setCanCheck($can): self
    {
        $this->canCheck = $can;

        return $this;
    }

    public function getPluginFile(): string
    {
        return $this->pluginFile;
    }

    public function getPluginId(): string
    {
        return $this->pluginId;
    }

    public function getPluginSlug(): string
    {
        return $this->pluginSlug;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function checkUpdate()
    {
        return $this->executeAction('check-update');
    }

    public function activateLicense()
    {
        return $this->executeAction('activate-license');
    }

    public function checkLicense()
    {
        return $this->executeAction('check-license');
    }

    public function deactivateLicense()
    {
        return $this->executeAction('deactivate-license');
    }

    protected function executeAction(string $name)
    {
        $action = $this->actions[$name] ?? null;

        if (! is_string($action) || ! class_exists($action)) {
            return false;
        }

        return (new $action)->execute();
    }
}
