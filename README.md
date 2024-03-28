<p align="center"><a href="https://vanrossum.dev" target="_blank"><img src="https://raw.githubusercontent.com/jeffreyvr/vanrossum.dev-art/main/logo.svg" width="320" alt="vanrossum.dev Logo"></a></p>

<p align="center">
<a href="https://packagist.org/packages/jeffreyvanrossum/wp-plugin-updater"><img src="https://img.shields.io/packagist/dt/jeffreyvanrossum/wp-plugin-updater" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/jeffreyvanrossum/wp-plugin-updater"><img src="https://img.shields.io/packagist/v/jeffreyvanrossum/wp-plugin-updater" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/jeffreyvanrossum/wp-plugin-updater"><img src="https://img.shields.io/packagist/l/jeffreyvanrossum/wp-plugin-updater" alt="License"></a>
</p>

# WP Plugin Updater

Allows you to check for updates for a plugin on a different server then wordpress.org.

> 🚸 As long as this package is still in development, the API might be subject to change and should not considered stable. Use at your own risk.

Made for a specific use case which may not fit everybody's needs.

Based on the [example plugin](https://github.com/Make-Lemonade/lemonsqueezy-wp-updater-example) from LemonSqueezy.

## Example

```php
(new Jeffreyvr\WPPluginUpdater\WPPluginUpdater(
    __FILE__, // plugin file path
    'your-plugin/your-plugin.php',
    'your-plugin',
    '1.0',
    "https://yourpluginsite.test/check-update",
    "https://yourpluginsite.test/validate-license"
))->setCanCheck(function() {
    // determine if check can be made
})
->setActions([
    // classes should implement Jeffreyvr\WPPluginUpdater\Action interface
    'activate-license' => ActivateLicenseAction::class,
    'deactivate-license' => DeactivateLicenseAction::class,
    'check-license' => CheckLicenseAction::class,
    'check-update' => CheckUpdateAction::class
]);

new \Jeffreyvr\WPPluginUpdater\Updater($this->updater);
```

## Contributors
* [Jeffrey van Rossum](https://github.com/jeffreyvr)
* [All contributors](https://github.com/jeffreyvr/wp-meta-box/graphs/contributors)

## License
MIT. Please see the [License File](/LICENSE) for more information.
