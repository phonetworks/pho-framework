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
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Graph extends \Pho\Lib\Graph\SubGraph implements ParticleInterface, ContextInterface
{

    use ParticleTrait;

    /**
     * ID generator is now Framework's own ID class
     *
     * @var string
     */
    protected $id_generator = ID::class;

    /**
     * Don't delete actors when the subgraph is deleted
     * Always!
     *
     * @var boolean
     */
    protected $no_member_deletion = true; // always

    public function __construct(Actor $creator, ContextInterface $context) 
    {
        parent::__construct($context);
        $this->creator = $creator;
        $this->creator_id = (string) $creator->id();
        $this
            ->addEdges("incoming", ActorOut\Write::class)
            ->initializeParticle();
    }

     /**
      * {@inheritdoc}
      */
    public function in(ContextInterface $context): bool
    {
        /*
        // would speed up, but not good for testing.
        if($context instanceof Space)
            return true;
        */
        $members = $context->members();
        foreach($members as $member) {
            if($member instanceof Graph) {
                if($member->id() == $this->id()) {
                    return true;
                }
                else {
                    if($this->in($member)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

}