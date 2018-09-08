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
 * Particle Interface
 * 
 * A "Particle" is a foundational Pho Framework node that constitutes
 * the basis of all other Pho Stack nodes.
 * 
 * This interface does not hold a method. The classes that implement 
 * this do it, in order to define that they are Pho Framework particles.
 * Otherwise, In Pho Framework, both {@link Actor} and {@link Obj} extend 
 * Pho\Lib\Graph\Node but {@link Frame} doesn't, it extends 
 * Pho\Lib\Graph\SubGraph which is also a subclass of Pho\Lib\Graph\Node
 * but with additional graph-like traits. Therefore this interface provides 
 * a common ground that all foundational nodes can be based off of, and we 
 * call them Particles.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
interface ParticleInterface
{
    
    /**
     * Retrieves the object's existential properties
     *
     * Existential properties are:
     * * The node itself ($this)
     * * Its context
     * * Its creator
     * 
     * These properties can never be altered.
     * 
     * @return array
     */
    public function existentials(): array;

}