<?php

namespace spec\EzSystems\HybridPlatformUi\Response;

use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Response;

class NotificationResponseSpec extends ObjectBehavior
{
    function it_is_a_response()
    {
        $this->shouldBeAnInstanceOf(Response::class);
    }
}
