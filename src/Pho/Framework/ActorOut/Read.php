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
 * Reads Edge
 * 
 * **"Reads"** is one of the three outgoing edges of the 
 * {@link Pho\Framework\Actor} particle. 
 * 
 * The "reads" name comes from the UNIX world. It represents 
 * consumption of existing {@link Pho\Framework\Obj}s.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Read extends Framework\AbstractEdge
{
    const HEAD_LABEL = "read";
    const HEAD_LABELS = "reads";
    const TAIL_LABEL = "reader";
    const TAIL_LABELS = "readers";
    const SETTABLES = [Framework\ParticleInterface::class];
}