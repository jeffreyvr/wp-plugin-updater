<?php

use Jeffreyvr\WPPluginUpdater\WPPluginUpdater;
use Tests\Fixtures\TestableUpdater;

function makePluginUpdater(string $slug = 'plugin-a'): WPPluginUpdater
{
    return new WPPluginUpdater(
        '/path/to/'.$slug.'.php',
        $slug.'/'.$slug.'.php',
        $slug,
        '1.0.0',
    );
}

function makeRemoteUpdate(string $version = '1.1.0'): object
{
    return (object) [
        'success' => true,
        'update' => (object) [
            'version' => $version,
            'download_link' => 'https://example.test/plugin.zip',
            'sections' => (object) [
                'description' => 'A plugin',
            ],
        ],
    ];
}

it('returns the previous result for a non plugin_information action', function () {
    $updater = new TestableUpdater(makePluginUpdater('plugin-a'));
    $previous = (object) ['name' => 'Other'];

    expect($updater->info($previous, 'query_plugins', (object) ['slug' => 'plugin-a']))
        ->toBe($previous);
});

it('returns the previous result when the slug does not match', function () {
    $updater = new TestableUpdater(makePluginUpdater('plugin-b'));
    $previous = (object) ['name' => 'Plugin A', 'slug' => 'plugin-a'];

    expect($updater->info($previous, 'plugin_information', (object) ['slug' => 'plugin-a']))
        ->toBe($previous);
});

it('does not clobber another updater response in the plugins_api chain', function () {
    $pluginA = new TestableUpdater(makePluginUpdater('plugin-a'));
    $pluginB = new TestableUpdater(makePluginUpdater('plugin-b'));

    $pluginA->remote = makeRemoteUpdate();
    $pluginB->remote = makeRemoteUpdate();

    $result = false;
    $args = (object) ['slug' => 'plugin-a'];

    $result = $pluginA->info($result, 'plugin_information', $args);
    $result = $pluginB->info($result, 'plugin_information', $args);

    expect($result)->toBeObject()
        ->and($result->slug)->toBe('plugin-a')
        ->and($result->name)->toBe('Test Plugin');
});

it('returns the previous result when the remote payload is missing', function () {
    $updater = new TestableUpdater(makePluginUpdater('plugin-a'));
    $updater->remote = false;
    $previous = (object) ['name' => 'Existing'];

    expect($updater->info($previous, 'plugin_information', (object) ['slug' => 'plugin-a']))
        ->toBe($previous);
});

it('maps plugin info when the remote payload is valid', function () {
    $updater = new TestableUpdater(makePluginUpdater('plugin-a'));
    $updater->remote = makeRemoteUpdate();

    $result = $updater->info(false, 'plugin_information', (object) ['slug' => 'plugin-a']);

    expect($result)->toBeObject()
        ->and($result->name)->toBe('Test Plugin')
        ->and($result->slug)->toBe('plugin-a')
        ->and($result->sections)->toBeArray()
        ->and($result->sections['description'])->toBe('A plugin');
});

it('returns a false transient unchanged', function () {
    $updater = new TestableUpdater(makePluginUpdater());

    expect($updater->update(false))->toBeFalse();
});

it('returns an empty transient unchanged', function () {
    $updater = new TestableUpdater(makePluginUpdater());
    $transient = (object) ['checked' => []];

    expect($updater->update($transient))->toBe($transient);
});

it('adds the plugin to no_update when no newer version is available', function () {
    $plugin = makePluginUpdater('plugin-a');
    $updater = new TestableUpdater($plugin);
    $updater->remote = makeRemoteUpdate('1.0.0');

    $transient = (object) [
        'checked' => [$plugin->getPluginId() => '1.0.0'],
        'response' => [],
        'no_update' => [],
    ];

    $result = $updater->update($transient);

    expect($result->no_update)->toHaveKey($plugin->getPluginId())
        ->and($result->response)->not->toHaveKey($plugin->getPluginId());
});

it('adds the plugin to response when a newer version is available', function () {
    $plugin = makePluginUpdater('plugin-a');
    $updater = new TestableUpdater($plugin);
    $updater->remote = makeRemoteUpdate('2.0.0');

    $transient = (object) [
        'checked' => [$plugin->getPluginId() => '1.0.0'],
        'response' => [],
        'no_update' => [],
    ];

    $result = $updater->update($transient);

    expect($result->response)->toHaveKey($plugin->getPluginId())
        ->and($result->response[$plugin->getPluginId()]->new_version)->toBe('2.0.0')
        ->and($result->response[$plugin->getPluginId()]->package)->toBe('https://example.test/plugin.zip');
});
