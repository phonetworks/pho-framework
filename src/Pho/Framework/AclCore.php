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

use Pho\Lib\Graph;

/**
 * AclCore (Access Control Lists Core)
 * 
 * Access Control List is a gateway authority between graph nodes.
 * Pho Framework introduces the core of this authority as an 
 * abstract implementation and expects the higher level packages
 * to extend itself. The core is by default immutable; meaning
 * the two values (creator and context) cannot be altered.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class AclCore {

    /**
     * Who created this node. Must point to an Actor.
     *
     * @var Actor
     */
    protected $creator;

    /**
     * In what context this node was created. Must point to a node 
     * that implements Pho\Lib\Graph\GraphInterface
     * @var Pho\Lib\Graph\GraphInterface
     */
    protected $context;

    /**
     * Constructor.
     * 
     * @param Actor $creator The creator of this node.
     * @param \Pho\Lib\Graph\GraphInterface $context The context in which this node is created and will exist
     */
    public function __construct(Actor $creator, Graph\GraphInterface $context) {
        $this->creator = $creator;
        $this->context = $context;
    }

    /**
     * Converts the object into a portable PHP array
     *
     * Useful for custom serialization.
     * 
     * @return array
     */
    public function toArray(): array
    {
        //eval(\Psy\sh());
        return [
            "creator" => (string) $this->creator->id(),
            "context" => ($this->context instanceof Graph\Graph) ? Graph\Graph::class : (string) $this->context->id()
        ]; 
    }

}