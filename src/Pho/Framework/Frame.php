<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Framework;

class Frame extends SubGraph implements NodeInterface {

    const EDGES_IN = [Actor\Reads::class, Actor\Subscribes::class, Actor\Writes::class, Object\Transmits::class];

}