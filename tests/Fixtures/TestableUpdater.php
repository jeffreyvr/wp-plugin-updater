<?php

namespace Tests\Fixtures;

use Jeffreyvr\WPPluginUpdater\Updater;

class TestableUpdater extends Updater
{
    public mixed $remote = false;

    public function request(): mixed
    {
        return $this->remote;
    }
}
