<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Framework\Actor;

use Pho\Framework;

class Writes extends Subscribes {

    const HEAD_LABEL = "write";
    const HEAD_LABELS = "writes";
    const TAIL_LABEL = "writer";
    const TAIL_LABELS = "writers";
    const SETTABLES = [Framework\Frame::class, Framework\Object::class]; /* inherits the values in Edits */

}