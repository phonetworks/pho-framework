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
 * The Object Node
 * 
 * One of the three foundational nodes in the Pho Framework.
 * Objects do one and only one thing, they "transmit"
 * And that is an optional edge, most object will not
 * have any outgoing edges.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Object extends AbstractNode /* !!DOES NOT!! implements ObjectInterface */ {

    const EDGES_IN = [ActorOut\Reads::class, ActorOut\Subscribes::class, ActorOut\Writes::class, ObjectOut\Transmits::class];

}