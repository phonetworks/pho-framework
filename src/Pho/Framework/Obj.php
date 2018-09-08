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
 * The Obj Particle
 * 
 * One of the three foundational nodes in the Pho Framework.
 * Obj (short for Object) do one and only one thing, they "transmit"
 * And that is an optional edge, most object will not
 * have any outgoing edges.
 * 
 * Please note, the reason why it's called Obj
 * internally is because starting with version 7.2, PHP no
 * longer allows the declaration of class name "Object" 
 * as it conflicts with its inner-workings. Hence, starting
 * with pho-framework 11.0, we will switch to Obj as an internal 
 * reference to Object particles.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Obj extends \Pho\Lib\Graph\Node implements ParticleInterface
{

    use ParticleTrait;
    
    /**
     * ID generator is now Framework's own ID class
     *
     * @var string
     */
    protected $id_generator = ID::class;

    public function __construct(Actor $creator, ContextInterface $context) 
    {
        parent::__construct($context);
        $this->creator = $creator;
        $this->creator_id = (string) $creator->id();
        $this
            ->addEdges("incoming", ActorOut\Write::class)
            ->addEdges("outgoing", ObjOut\Mention::class)
            ->initializeParticle();
    }

}
