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
 * The Object Particle
 * 
 * One of the three foundational nodes in the Pho Framework.
 * Objects do one and only one thing, they "transmit"
 * And that is an optional edge, most object will not
 * have any outgoing edges.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Object extends \Pho\Lib\Graph\Node implements ParticleInterface, \SplObserver, \SplSubject
{

    use ParticleTrait {
        ParticleTrait::__construct as particleConstructor;
    }

    public function __construct(Actor $creator, ContextInterface $context) 
    {
        parent::__construct($context);
        $this->creator = $creator;
        $this->creator_id = (string) $creator->id();
        $this->registerIncomingEdges(ActorOut\Write::class);
        $this->particleConstructor();
    }

}
