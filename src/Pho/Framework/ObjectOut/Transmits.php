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

class Transmits extends Framework\AbstractEdge {

    const HEAD_LABEL = "receiver";
    const HEAD_LABELS = "receivers";
    const TAIL_LABEL = "transmitter";
    const TAIL_LABELS = "transmitters";

    const SETTABLES = [Framework\ParticleInterface::class];

}