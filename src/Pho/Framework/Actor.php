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

/**
 * The Actor Node
 * 
 * Actors have three outgoing edges:
 * * {@link Actor\Reads}
 * * {@link Actor\Writes}
 * * {@link Actor\Subscribes}
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Actor extends AbstractNode /* !!DOES NOT!! implements ActorInterface */ {

    const EDGES_IN = [ActorOut\Reads::class, ActorOut\Subscribes::class, ObjectOut\Transmits::class];

}