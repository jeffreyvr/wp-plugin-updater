<?php

namespace Jeffreyvr\WPPluginUpdater;

use Closure;

class WPPluginUpdater
{
    public array|Closure $checkLicenseRequestBody;

    public array|Closure $checkUpdateRequestBody;

    public bool|Closure $canCheck = false;

    public function __construct(
        public string $pluginFile,
        public string $pluginId,
        public string $pluginSlug,
        public string $version,
        public string $checkUpdateUrl,
        public string $checkLicenseUrl
    ) {
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

    public function setCheckLicenseRequestBody($body): self
    {
        $this->checkLicenseRequestBody = $body;

        return $this;
    }

    public function setCheckUpdateRequestBody($body): self
    {
        $this->checkUpdateRequestBody = $body;

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
        $body = $this->checkUpdateRequestBody;

        $response = wp_remote_get($this->checkUpdateUrl, [
            'body' => is_callable($body) ? $body() : $body,
            'timeout' => 10,
        ]);

        return $response;
    }

    public function checkLicense()
    {
        $body = $this->checkLicenseRequestBody;

        $response = wp_remote_post($this->checkLicenseUrl, [
            'body' => is_callable($body) ? $body() : $body,
            'timeout' => 10,
        ]);

        return $response;
    }
}
