<?php

namespace EzSystems\HybridPlatformUi\Response;

use Symfony\Component\HttpFoundation\Response;

class NotificationResponse extends Response
{
    public static function success()
    {
        return static::create('');
    }

    public static function error()
    {
        return static::create('');
    }
}
