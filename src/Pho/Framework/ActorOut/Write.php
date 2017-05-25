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
 * Writes Edge
 * 
 * **"Writes"** is one of the three outgoing edges of the 
 * {@link Pho\Framework\Actor} particle. 
 * 
 * The "writes" name comes from the UNIX world. It represents 
 * creation or editing of new {@link Pho\Framework\Object}s.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Write extends Subscribe {

    const HEAD_LABEL = "write";
    const HEAD_LABELS = "writes";
    const TAIL_LABEL = "writer";
    const TAIL_LABELS = "writers";
    const SETTABLES = [Framework\Frame::class, Framework\Object::class]; /* inherits the values in Edits */

}