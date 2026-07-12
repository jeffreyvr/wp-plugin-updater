<?php

use Jeffreyvr\WPPluginUpdater\WPPluginUpdater;
use Tests\Fixtures\RecordingAction;

function makeUpdater(): WPPluginUpdater
{
    return new WPPluginUpdater(
        '/path/to/plugin.php',
        'plugin/plugin.php',
        'plugin',
        '1.0.0',
    );
}

beforeEach(function () {
    RecordingAction::reset();
});

it('returns false for canCheck by default', function () {
    expect(makeUpdater()->canCheck())->toBeFalse();
});

it('respects a boolean canCheck value', function () {
    $updater = makeUpdater()->setCanCheck(true);

    expect($updater->canCheck())->toBeTrue();
});

it('resolves canCheck from a callable', function () {
    $updater = makeUpdater()->setCanCheck(fn () => true);

    expect($updater->canCheck())->toBeTrue();
});

it('returns false when an action class is not configured', function () {
    $updater = makeUpdater();

    expect($updater->checkUpdate())->toBeFalse()
        ->and($updater->activateLicense())->toBeFalse()
        ->and($updater->checkLicense())->toBeFalse()
        ->and($updater->deactivateLicense())->toBeFalse();
});

it('returns false when an action is not a string class name', function () {
    $updater = makeUpdater()->setAction('check-update', new RecordingAction);

    expect($updater->checkUpdate())->toBeFalse()
        ->and(RecordingAction::$executions)->toBe(0);
});

it('returns false when an action class does not exist', function () {
    $updater = makeUpdater()->setAction('check-update', 'Tests\\Fixtures\\MissingAction');

    expect($updater->checkUpdate())->toBeFalse();
});

it('instantiates and executes configured action classes', function () {
    RecordingAction::$returnValue = ['success' => true];

    $updater = makeUpdater()->setActions([
        'activate-license' => RecordingAction::class,
        'deactivate-license' => RecordingAction::class,
        'check-license' => RecordingAction::class,
        'check-update' => RecordingAction::class,
    ]);

    expect($updater->activateLicense())->toBe(['success' => true])
        ->and($updater->deactivateLicense())->toBe(['success' => true])
        ->and($updater->checkLicense())->toBe(['success' => true])
        ->and($updater->checkUpdate())->toBe(['success' => true])
        ->and(RecordingAction::$executions)->toBe(4);
});

it('exposes plugin metadata getters', function () {
    $updater = makeUpdater();

    expect($updater->getPluginFile())->toBe('/path/to/plugin.php')
        ->and($updater->getPluginId())->toBe('plugin/plugin.php')
        ->and($updater->getPluginSlug())->toBe('plugin')
        ->and($updater->getVersion())->toBe('1.0.0');
});
