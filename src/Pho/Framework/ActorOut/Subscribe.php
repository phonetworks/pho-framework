<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Framework\ActorOut;

use Pho\Framework;
use Pho\Framework\Actor;

/**
 * Subscribe Edge
 * 
 * **"Subscribe"** is one of the three outgoing edges of the 
 * {@link Pho\Framework\Actor} particle. 
 * 
 * When an actor subscribes to another node, it declares intent
 * to listen for updates from that node.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Subscribe extends Framework\AbstractEdge
{

    const HEAD_LABEL = "subscription";
    const HEAD_LABELS = "subscriptions";
    const TAIL_LABEL = "subscriber";
    const TAIL_LABELS = "subscribers";
    const SETTABLES = [Framework\ParticleInterface::class];

    protected function execute(): void
    {
        if(
            isset($this->notification) &&
            !$this->orphan() &&
            $this->head()->node() instanceof Actor
        )
            $this->head()->node()->notify($this->notification);
    }
}