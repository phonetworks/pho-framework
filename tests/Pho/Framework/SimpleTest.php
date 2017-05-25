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

use Pho\Lib\Graph\Predicate;

class SimpleTest extends \PHPUnit\Framework\TestCase 
{
    private $graph;

    public function setUp() {
        $this->graph = new Graph();
    }

    public function tearDown() {
        unset($this->graph);
    }

    public function testActor() {
        $node = new Actor($this->graph);
        $node_expected_to_be_identical = $this->graph->get($node->id());
        $this->assertEquals($node->id(), $node_expected_to_be_identical->id());
    }

    public function testActorEdge() {
        $actor = new Actor($this->graph);
        $object = new Object($actor, $this->graph);
        $edge = $actor->write($object);
        $this->assertInstanceOf(ActorOut\Write::class, $edge);
        $this->assertInstanceOf(Predicate::class, $edge->predicate());
    }

    public function testActorPredicate() {
        $actor = new Actor($this->graph);
        $object = new Object($actor, $this->graph);
        $edge = $actor->subscribe($object);
        $this->assertInstanceOf(ActorOut\SubscribePredicate::class, $edge->predicate());
    }

    public function testObjectGetter() {
        $actor = new Actor($this->graph);
        $object = new Object($actor, $this->graph);
        $edge = $actor->write($object);
        $this->assertInstanceOf(ActorOut\Write::class, $object->edges()->in()->current());
        $this->assertInstanceOf(Actor::class, $object->getWriters()[0]);
        $this->assertCount(1, $object->getWriters());
        $this->assertCount(1, $actor->getWrites());
        $this->assertInstanceOf(ActorOut\Write::class, $actor->edges()->out()->current());
        $this->assertInstanceOf(Object::class, $actor->getWrites()[0]);
    }

    public function testObjectHaser() {
        $actor = new Actor($this->graph);
        $object = new Object($actor, $this->graph);
        $edge = $actor->write($object);
        $this->assertTrue($object->hasWriter($actor->id()));
        $this->assertTrue($actor->hasWrite($object->id()));
        $this->assertFalse($actor->hasWrite($this->graph->id()));
        $this->assertFalse($actor->hasWrite($actor->id()));
    }

     public function testFiltering() {
        $actor = new Actor($this->graph);
        $object = new Object($actor, $this->graph);
        $edge = $actor->write($object);
        $edge = $actor->reads($object);
        $this->assertCount(1, $actor->getWrites());
    }

    /**
     * Since write extends subscribes
     */
    public function testEdgeInheritance() {
        $actor = new Actor($this->graph);
        $object = new Object($actor, $this->graph);
        $edge = $actor->write($object);
        $this->assertCount(1, $actor->getSubscriptions());
        $this->assertCount(0, $actor->getReads());
    }

    /**
     * @expectedException     Pho\Framework\Exceptions\InvalidEdgeHeadTypeException
     */
    public function testImpossibleEdge() {
        $actor1 = new Actor($this->graph);
        $actor2 = new Actor($this->graph);
        $edge = $actor1->write($actor2);
    }

    public function testEdgeInvoke() {
        $actor = new Actor($this->graph);
        $object = new Object($actor, $this->graph);
        $edge = $actor->write($object);
        $this->assertInstanceOf(Object::class, $edge());
        $this->assertEquals($object->id(), $edge()->id());
    }


    public function testActorToArray() {
        $actor = new Actor($this->graph);
        $array = $actor->toArray();
        $faker = \Faker\Factory::create();
        $this->assertArrayHasKey("id", $array);
        $this->assertArrayHasKey("attributes", $array);
        $this->assertCount(0, $array["attributes"]);
        $actor->attributes()->username = $faker->username;
        $this->assertCount(1, $actor->toArray()["attributes"]);
        $this->assertArrayHasKey("edge_list", $array);
        $this->assertArrayHasKey("creator", $array);
        $this->assertEquals($array["creator"], $actor->id());
    }

    public function testFrameToArray() {
        $faker = \Faker\Factory::create();
        $actor = new Actor($this->graph);
        $frame = new Frame($actor, $this->graph);
        $edge = $actor->write($frame);
        $array = $frame->toArray();
        $this->assertArrayHasKey("id", $array);
        $this->assertArrayHasKey("attributes", $array);
        $this->assertCount(0, $array["attributes"]);
        $actor->attributes()->username = $faker->username;
        $this->assertCount(1, $actor->toArray()["attributes"]);
        $this->assertArrayHasKey("edge_list", $array);
        $this->assertArrayHasKey("creator", $array);
        $this->assertEquals($array["creator"], $actor->id());
    }

    public function testContextInterface() {
        $this->assertInstanceOf(ContextInterface::class, $this->graph);
        $actor = new Actor($this->graph);
        $frame = new Frame($actor, $this->graph);
        $this->assertInstanceOf(ContextInterface::class, $frame);
    }

    public function testExistentials() {
        $actor = new Actor($this->graph);
        $frame = new Frame($actor, $this->graph);
        $this->assertCount(3, $actor->existentials());
        $this->assertEquals($frame, $frame->existentials()["node"]);
        $this->assertEquals($actor, $frame->existentials()["creator"]);
        $this->assertEquals($this->graph, $frame->existentials()["context"]);
    }

}