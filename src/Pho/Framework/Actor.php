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
 * The Actor Particle
 * 
 * Actors have three outgoing edges:
 * * {@link Actor\Reads}
 * * {@link Actor\Writes}
 * * {@link Actor\Subscribes}
 * 
 * @method AbstractEdge reads(ParticleInterface $particle)
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Actor extends \Pho\Lib\Graph\Node implements ParticleInterface {

    use ParticleTrait;

    /**
     * Incoming Edges
     * 
     * A constant node property of edges that are directed towards this node.
     * 
     * @var array An array of class names (with their namespaces)
     */
    const EDGES_IN = [ActorOut\Reads::class, ActorOut\Subscribes::class, ObjectOut\Transmits::class];

}