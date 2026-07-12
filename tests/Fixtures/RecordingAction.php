<?php

namespace Tests\Fixtures;

use Jeffreyvr\WPPluginUpdater\Action;

class RecordingAction implements Action
{
    public static int $executions = 0;

    public static mixed $returnValue = 'ok';

    public function execute(): mixed
    {
        self::$executions++;

        return self::$returnValue;
    }

    public static function reset(): void
    {
        self::$executions = 0;
        self::$returnValue = 'ok';
    }
}
