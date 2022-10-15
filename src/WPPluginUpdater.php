<?php

namespace Jeffreyvr\WPPluginUpdater;

use Closure;

class WPPluginUpdater
{
    public string|Closure $licenseKey;

    public function __construct(
        public string $pluginFile,
        public string $pluginId,
        public string $pluginSlug,
        public string $version,
        public string $checkUpdateUrl,
        public string $checkLicenseUrl
    ) {

    }

    public function setLicenseKey($licenseKey): self
    {
        $this->licenseKey = $licenseKey;

        return $this;
    }

    public function getLicenseKey()
    {
        if(is_callable($this->licenseKey)) {
            $callback = $this->licenseKey;

            return $callback();
        }

        return $this->licenseKey;
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

    public function getFrom(): string
    {
        return parse_url( get_site_url(), PHP_URL_HOST );
    }

    function checkUpdate()
    {
        $response = wp_remote_get($this->checkUpdateUrl, [
            'body' => [
                'license_key' => $this->getLicenseKey()
            ],
            'timeout' => 10
        ]);

        return $response;
    }

    public function checkLicense()
    {
        $response = wp_remote_post($this->checkLicenseUrl, [
            'body' => [
                'license_key' => $this->getLicenseKey(),
                'from' => $this->getFrom()
            ],
            'timeout' => 10
        ]);

        return $response;
    }
}