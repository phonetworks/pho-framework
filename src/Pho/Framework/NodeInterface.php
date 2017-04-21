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
 * Node Interface
 * 
 * Merely a placeholder. Does not hold a method except defining
 * the classes that implement it that they are actually Pho
 * Framework compatible nodes.
 * 
 * In Pho Framework, both {@link Actor} and {@link Object} extend 
 * {@link AbstractNode} but {@link Frame} doesn't. 
 * Therefore this interface provides a common ground that all 
 * foundational nodes can be based off of.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
interface NodeInterface {
    
}