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
 * The Frame Particle
 * 
 * At its core, Frame is a graph, or more specifically, a subgraph.
 * It extends the Pho\Lib\Graph\SubGraph class, which is a regular node,
 * as well as a Graph (by way of using the Pho\Lib\Graph\ClusterTrait)
 * both at the same time. It implements both Pho\Lib\Graph\GraphInterface
 * and Pho\Lib\Graph\NodeInterface. In order to prevent
 * any confusions with Pho\Lib\Graph's nomenclature, this class is called
 * Frame instead.
 * 
 * In contrast to other particles, Frame doesn't contain edges but 
 * its **"contains"** method acts similarly to an edge.
 * 
 * 
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Frame extends \Pho\Lib\Graph\SubGraph implements ParticleInterface, ContextInterface {

    use ParticleTrait;

    /**
     * Incoming Edges
     * 
     * A constant node property of edges that are directed towards this node.
     * 
     * @var array An array of class names (with their namespaces)
     */
    const EDGES_IN = [ActorOut\Reads::class, ActorOut\Subscribes::class, ActorOut\Writes::class, ObjectOut\Transmits::class];

    public function __construct(Actor $creator, ContextInterface $context) {
        parent::__construct($context);
        $this->existentials = new Existentials($this, $creator, $context);
        $this->setupEdges();
    }

     /**
     * {@inheritdoc}
     */
    public function belongsOrEquals(ContextInterface $context): bool
    {
        /*if($context instanceof Graph)
            return true;*/
        $members = $context->members();
        foreach($members as $member) {
            if($member instanceof Frame) {
                if($member->id() == $this->id()) {
                    return true;
                }
                else {
                    if($this->belongsOrEquals($member)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

}