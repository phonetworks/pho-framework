<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// namespace Pho\Framework\Tests;

use Pho\Framework;
use Pho\Lib\Graph;

class SimpleTest extends \PHPUnit\Framework\TestCase 
{
    private $graph;

    public function setUp() {
        $this->graph = new Graph\Graph();
    }

    public function tearDown() {
        unset($this->graph);
    }

    public function testActor() {
        $node = new Framework\Actor($this->graph);
        $node_expected_to_be_identical = $this->graph->get($node->id());
        $this->assertEquals($node->id(), $node_expected_to_be_identical->id());
    }

    public function testActorEdge() {
        $actor = new Framework\Actor($this->graph);
        $object = new Framework\Object($this->graph);
        $edge = $actor->writes($object);
        $this->assertInstanceOf(Framework\Actor\Writes::class, $edge);
        $this->assertInstanceOf(Graph\Predicate::class, $edge->predicate());
    }

    public function testActorPredicate() {
        $actor = new Framework\Actor($this->graph);
        $object = new Framework\Object($this->graph);
        $edge = $actor->subscribes($object);
        $this->assertInstanceOf(Framework\Actor\SubscribesPredicate::class, $edge->predicate());
    }

    public function testObjectGetter() {
        $actor = new Framework\Actor($this->graph);
        $object = new Framework\Object($this->graph);
        $edge = $actor->writes($object);
        $this->assertInstanceOf(Framework\Actor\Writes::class, $object->getWriters()[0]);
        $this->assertCount(1, $object->getWriters());
        $this->assertCount(1, $actor->getWrites());
        $this->assertInstanceOf(Framework\Actor\Writes::class, $actor->getWrites()[0]);
    }

     public function testFiltering() {
        $actor = new Framework\Actor($this->graph);
        $object = new Framework\Object($this->graph);
        $edge = $actor->writes($object);
        $edge = $actor->reads($object);
        $this->assertCount(1, $actor->getWrites());
    }

    /**
     * Since write extends subscribes
     */
    public function testEdgeInheritance() {
        $actor = new Framework\Actor($this->graph);
        $object = new Framework\Object($this->graph);
        $edge = $actor->writes($object);
        $this->assertCount(1, $actor->getSubscriptions());
        $this->assertCount(0, $actor->getReads());
    }

    /**
     * @expectedException     Pho\Framework\Exceptions\InvalidEdgeHeadTypeException
     */
    public function testImpossibleEdge() {
        $actor1 = new Framework\Actor($this->graph);
        $actor2 = new Framework\Actor($this->graph);
        $edge = $actor1->writes($actor2);
    }


    

}