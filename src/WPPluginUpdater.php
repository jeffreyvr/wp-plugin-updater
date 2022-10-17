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
        if (! $this->actions['check-update'] instanceof Action) {
            return false;
        }

        return (new $this->actions['check-update'])->execute();
    }

    public function activateLicense()
    {
        if (! $this->actions['activate-license'] instanceof Action) {
            return false;
        }

        return $this->actions['activate-license']->execute();
    }

    public function checkLicense()
    {
        if (! $this->actions['check-license'] instanceof Action) {
            return false;
        }

        return (new $this->actions['check-license'])->execute();
    }

    public function deactivateLicense()
    {
        if (! $this->actions['deactivate-license'] instanceof Action) {
            return false;
        }

        return (new $this->actions['deactivate-license'])->execute();
    }
}
