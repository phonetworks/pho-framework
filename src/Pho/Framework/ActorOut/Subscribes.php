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

/**
 * Subscribes Edge
 * 
 * **"Subscribes"** is one of the three outgoing edges of the 
 * {@link Pho\Framework\Actor} particle. 
 * 
 * When an actor subscribes to another node, it declares intent
 * to listen for updates from that node.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Subscribes extends Framework\AbstractEdge {

    const HEAD_LABEL = "subscription";
    const HEAD_LABELS = "subscriptions";
    const TAIL_LABEL = "subscriber";
    const TAIL_LABELS = "subscribers";
    const SETTABLES = [Framework\ParticleInterface::class];

}