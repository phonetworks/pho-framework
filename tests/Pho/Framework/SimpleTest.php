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
        $node = new Actor($this->graph);
        $node_expected_to_be_identical = $this->graph->get($node->id());
        $this->assertEquals($node->id(), $node_expected_to_be_identical->id());
    }

    public function testActorEdge() {
        $actor = new Actor($this->graph);
        $object = new Object($actor, $this->graph);
        $edge = $actor->writes($object);
        $this->assertInstanceOf(ActorOut\Writes::class, $edge);
        $this->assertInstanceOf(Graph\Predicate::class, $edge->predicate());
    }

    public function testActorPredicate() {
        $actor = new Actor($this->graph);
        $object = new Object($actor, $this->graph);
        $edge = $actor->subscribes($object);
        $this->assertInstanceOf(ActorOut\SubscribesPredicate::class, $edge->predicate());
    }

    public function testObjectGetter() {
        $actor = new Actor($this->graph);
        $object = new Object($actor, $this->graph);
        $edge = $actor->writes($object);
        $this->assertInstanceOf(ActorOut\Writes::class, $object->getWriters()[0]);
        $this->assertCount(1, $object->getWriters());
        $this->assertCount(1, $actor->getWrites());
        $this->assertInstanceOf(ActorOut\Writes::class, $actor->getWrites()[0]);
    }

     public function testFiltering() {
        $actor = new Actor($this->graph);
        $object = new Object($actor, $this->graph);
        $edge = $actor->writes($object);
        $edge = $actor->reads($object);
        $this->assertCount(1, $actor->getWrites());
    }

    /**
     * Since write extends subscribes
     */
    public function testEdgeInheritance() {
        $actor = new Actor($this->graph);
        $object = new Object($actor, $this->graph);
        $edge = $actor->writes($object);
        $this->assertCount(1, $actor->getSubscriptions());
        $this->assertCount(0, $actor->getReads());
    }

    /**
     * @expectedException     Pho\Framework\Exceptions\InvalidEdgeHeadTypeException
     */
    public function testImpossibleEdge() {
        $actor1 = new Actor($this->graph);
        $actor2 = new Actor($this->graph);
        $edge = $actor1->writes($actor2);
    }

    public function testEdgeInvoke() {
        $actor = new Actor($this->graph);
        $object = new Object($actor, $this->graph);
        $edge = $actor->writes($object);
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
        $this->assertArrayHasKey("acl", $array);
        $this->assertCount(2, $array["acl"]);
        $this->assertArrayHasKey("context", $array["acl"]);
        $this->assertArrayHasKey("creator", $array["acl"]);
    }

    public function testFrameToArray() {
        $faker = \Faker\Factory::create();
        $actor = new Actor($this->graph);
        $frame = new Frame($actor, $this->graph);
        $edge = $actor->writes($frame);
        $array = $frame->toArray();
        $this->assertArrayHasKey("id", $array);
        $this->assertArrayHasKey("attributes", $array);
        $this->assertCount(0, $array["attributes"]);
        $actor->attributes()->username = $faker->username;
        $this->assertCount(1, $actor->toArray()["attributes"]);
        $this->assertArrayHasKey("edge_list", $array);
        $this->assertArrayHasKey("acl", $array);
        $this->assertCount(2, $array["acl"]);
        $this->assertArrayHasKey("context", $array["acl"]);
        $this->assertArrayHasKey("creator", $array["acl"]);
        $this->assertEquals($actor->id(), $array["acl"]["creator"]);
    }

}