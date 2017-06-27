<?php

namespace Pho\Framework\ActorOut;

class WriteNotification extends \Pho\Framework\AbstractNotification
{
    const MSG = "%s has written something.";

    public function __toString(): string
    {
        return sprintf(self::MSG, (string) $this()->tail()->id());
    }
}