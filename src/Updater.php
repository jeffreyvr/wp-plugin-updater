<?php

namespace Jeffreyvr\WPPluginUpdater;

use stdClass;
use WP_Upgrader;

class Updater
{
    public string $cacheKey;

    public bool $cacheEnabled;

    public function __construct(
        public WPPluginUpdater $pluginUpdater
    ) {
        $this->cacheKey = str_replace('-', '_', $this->pluginUpdater->getPluginSlug()).'_updater';

        $this->cacheEnabled = true;

        // add_filter('https_ssl_verify', '__return_false');

        add_filter('plugins_api', [$this, 'info'], 20, 3);
        add_filter('site_transient_update_plugins', [$this, 'update']);
        add_action('upgrader_process_complete', [$this, 'purge'], 10, 2);
    }

    public function request()
    {
        if (! $this->pluginUpdater->canCheck()) {
            return false;
        }

        $remote = get_transient($this->cacheKey);

        if (false !== $remote && $this->cacheEnabled) {
            if ('error' === $remote) {
                return false;
            }

            return json_decode($remote);
        }

        $remote = $this->pluginUpdater->checkUpdate();

        if (
            is_wp_error($remote)
            || 200 !== wp_remote_retrieve_response_code($remote)
            || empty(wp_remote_retrieve_body($remote))
        ) {
            set_transient($this->cacheKey, 'error', MINUTE_IN_SECONDS * 10);

            return false;
        }

        $payload = wp_remote_retrieve_body($remote);

        set_transient($this->cacheKey, $payload, DAY_IN_SECONDS);

        return json_decode($payload);
    }

    /**
     * Override the WordPress request to return the correct plugin info.
     *
     * @see https://developer.wordpress.org/reference/hooks/plugins_api/
     */
    public function info(bool|object|array $result, string $action, object $args): object|bool
    {
        if ('plugin_information' !== $action) {
            return false;
        }

        if ($this->pluginUpdater->getPluginSlug() !== $args->slug) {
            return false;
        }

        $remote = $this->request();

        if (! $remote || ! $remote->success || empty($remote->update)) {
            return false;
        }

        $plugin_data = get_plugin_data($this->pluginUpdater->getPluginFile());

        $result = $remote->update;

        $result->name = $plugin_data['Name'];

        $result->slug = $this->pluginUpdater->getPluginSlug();

        $result->sections = (array) $result->sections;

        return $result;
    }

    /**
     * Override the WordPress request to check if an update is available.
     *
     * @see https://make.wordpress.org/core/2020/07/30/recommended-usage-of-the-updates-api-to-support-the-auto-updates-ui-for-plugins-and-themes-in-wordpress-5-5/
     */
    public function update(object|bool $transient): object|bool
    {
        if (empty($transient->checked)) {
            return $transient;
        }

        $res = (object) [
            'id' => $this->pluginUpdater->getPluginId(),
            'slug' => $this->pluginUpdater->getPluginSlug(),
            'plugin' => $this->pluginUpdater->getPluginId(),
            'new_version' => $this->pluginUpdater->getVersion(),
            'url' => '',
            'package' => '',
            'icons' => [],
            'banners' => [],
            'banners_rtl' => [],
            'tested' => '',
            'requires_php' => '',
            'compatibility' => new stdClass(),
        ];

        $remote = $this->request();

        if (
            $remote && $remote->success && ! empty($remote->update)
            && version_compare($this->pluginUpdater->getVersion(), $remote->update->version, '<')
        ) {
            $res->new_version = $remote->update->version;
            $res->package = $remote->update->download_link;

            $transient->response[$res->plugin] = $res;
        } else {
            $transient->no_update[$res->plugin] = $res;
        }

        return $transient;
    }

    /**
     * When the update is complete, purge the cache.
     *
     * @see https://developer.wordpress.org/reference/hooks/upgrader_process_complete/
     */
    public function purge(WP_Upgrader $upgrader, array $options): void
    {
        if (
            $this->cacheEnabled
            && 'update' === $options['action']
            && 'plugin' === $options['type']
            && ! empty($options['plugins'])
        ) {
            foreach ($options['plugins'] as $plugin) {
                if ($plugin === $this->pluginUpdater->getPluginId()) {
                    delete_transient($this->cacheKey);
                }
            }
        }
    }
}
