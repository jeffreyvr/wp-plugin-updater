# WP Plugin Updater

Allows you to check for updates for a plugin on a different server then wordpress.org.

## ⚠️ Under development

As long as this package is still in development, the API might be subject to change and should not considered stable. Use at your own risk.


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
->setCheckLicenseRequestBody(function() {
    // return the body (array) for the check license request
})
->setCheckUpdateRequestBody(function(){
    // return the body (array) for the plugin update request 
});

new \Jeffreyvr\WPPluginUpdater\Updater($this->updater);
```

## Contributors
* [Jeffrey van Rossum](https://github.com/jeffreyvr)
* [All contributors](https://github.com/jeffreyvr/wp-meta-box/graphs/contributors)

## License
MIT. Please see the [License File](/LICENSE) for more information.