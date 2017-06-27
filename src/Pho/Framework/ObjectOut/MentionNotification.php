<?php

namespace Pho\Framework\ObjectOut;

class MentionNotification extends \Pho\Framework\AbstractNotification
{
    const MSG = "%s has mentioned you in";

    public function __toString(): string
    {
        return sprintf(self::MSG, (string) $this()->tail()->creator()->id());
    }
}