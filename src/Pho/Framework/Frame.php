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

use Pho\Lib\Graph\SubGraph;

/**
 * The Frame Node
 * 
 * At its core, Frame is a graph, or more specifically, a subgraph.
 * It extends the Pho\Lib\Graph\SubGraph class, which is a regular node,
 * as well as a Graph (by way of using the Pho\Lib\Graph\ClusterTrait)
 * both at the same time. It implements both Pho\Lib\Graph\GraphInterface
 * and Pho\Lib\Graph\NodeInterface. In order to prevent
 * any confusions with Pho\Lib\Graph's nomenclature, this class is called
 * Frame instead.
 * 
 * In contrast to other (particles?), Frame doesn't contain edges but 
 * its **"contains"** edge acts similarly to an edge.
 * 
 * 
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Frame extends SubGraph implements NodeInterface {

    const EDGES_IN = [ActorOut\Reads::class, ActorOut\Subscribes::class, ActorOut\Writes::class, ObjectOut\Transmits::class];

}