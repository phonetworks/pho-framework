<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Framework\ObjectOut;

use Pho\Framework;

/**
 * Transmits Edge
 * 
 * **"Transmits"** is the only outgoing edge of the 
 * {@link Pho\Framework\Object} particle. It links the
 * Object with other nodes, allowing message passing.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Transmit extends Framework\AbstractEdge {

    const HEAD_LABEL = "receiver";
    const HEAD_LABELS = "receivers";
    const TAIL_LABEL = "transmitter";
    const TAIL_LABELS = "transmitters";

    const SETTABLES = [Framework\ParticleInterface::class];

}