<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Pho\Framework\ObjOut;

/**
 * The mention notification.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class MentionNotification extends \Pho\Framework\AbstractNotification
{
    const MSG = "%s has mentioned you in";

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return sprintf(self::MSG, (string) $this()->tail()->creator()->id());
    }
}