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

//use Pho\Lib\Graph\Predicate;

class SimpleTest extends \PHPUnit\Framework\TestCase 
{
    private $space;

    public function setUp() {
        $this->space = new Space();
    }

    public function tearDown() {
        unset($this->space);
    }

    public function testActor() {
        $node = new Actor($this->space);
        $node_expected_to_be_identical = $this->space->get($node->id());
        $this->assertEquals($node->id(), $node_expected_to_be_identical->id());
    }

    public function testActorEdge() {
        $actor = new Actor($this->space);
        $object = new Obj($actor, $this->space);
        $edge = $actor->write($object);
        $this->assertInstanceOf(ActorOut\Write::class, $edge);
        $this->assertInstanceOf(Predicate::class, $edge->predicate());
    }

    public function testActorPredicate() {
        $actor = new Actor($this->space);
        $object = new Obj($actor, $this->space);
        $edge = $actor->subscribe($object);
        $this->assertInstanceOf(ActorOut\SubscribePredicate::class, $edge->predicate());
    }

    public function testObjectGetter() {
        $actor = new Actor($this->space);
        $object = new Obj($actor, $this->space);
        $edge = $actor->write($object);
        $this->assertInstanceOf(ActorOut\Write::class, $object->edges()->in()->current());
        $this->assertInstanceOf(Actor::class, $object->getWriters()[0]);
        $this->assertCount(1, $object->getWriters());
        $this->assertCount(1, $actor->getWrites());
        $this->assertInstanceOf(ActorOut\Write::class, $actor->edges()->out()->current());
        $this->assertInstanceOf(Obj::class, $actor->getWrites()[0]);
    }


    public function testObjectHaser() {
        $actor = new Actor($this->space);
        $object = new Obj($actor, $this->space);
        $GLOBALS["dur"] = true;
        $edge = $actor->write($object);
        $this->assertTrue($object->hasWriter($actor->id()));
        $this->assertTrue($actor->hasWrite($object->id()));
        $this->assertFalse($actor->hasWrite($this->space->id()));
        $this->assertFalse($actor->hasWrite($actor->id()));
    }

     public function testFiltering() {
        $actor = new Actor($this->space);
        $object = new Obj($actor, $this->space);
        $edge = $actor->write($object);
        $edge = $actor->read($object);
        $this->assertCount(1, $actor->getWrites());
    }

    /**
     * Since write extends subscribes
     */
    public function testEdgeInheritance() {
        $actor = new Actor($this->space);
        $object = new Obj($actor, $this->space);
        $edge = $actor->write($object);
        $this->assertCount(1, $actor->getSubscriptions());
        $this->assertCount(0, $actor->getReads());
    }

    /**
     * @expectedException     Pho\Framework\Exceptions\InvalidEdgeHeadTypeException
     */
    public function testImpossibleEdge() {
        $actor1 = new Actor($this->space);
        $actor2 = new Actor($this->space);
        $edge = $actor1->write($actor2);
    }

    public function testEdgeInvoke() {
        $actor = new Actor($this->space);
        $object = new Obj($actor, $this->space);
        $edge = $actor->write($object);
        $this->assertInstanceOf(Obj::class, $edge());
        $this->assertEquals($object->id(), $edge()->id());
    }


    public function testActorToArray() {
        $actor = new Actor($this->space);
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

    public function testGraphToArray() {
        $faker = \Faker\Factory::create();
        $actor = new Actor($this->space);
        $graph = new Graph($actor, $this->space);
        $edge = $actor->write($graph);
        $array = $graph->toArray();
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
        $this->assertInstanceOf(ContextInterface::class, $this->space);
        $actor = new Actor($this->space);
        $graph = new Graph($actor, $this->space);
        $this->assertInstanceOf(ContextInterface::class, $graph);
    }

    public function testExistentials() {
        $actor = new Actor($this->space);
        $graph = new Graph($actor, $this->space);
        $this->assertCount(3, $actor->existentials());
        $this->assertEquals($graph, $graph->existentials()["node"]);
        $this->assertEquals($actor, $graph->existentials()["creator"]);
        $this->assertEquals($this->space, $graph->existentials()["context"]);
    }

    public function testInjectableFail() {
        $mock_kernel = \Mockery::mock('StdClass');
        $mock_kernel->shouldReceive('graph')->andReturn($this->space);
        $actor = new Actor($this->space);
        $object = new Obj($actor, $this->space);
        $edge = new class($actor, $object) extends ActorOut\Write {
            public function graph() {
                return $this->injection("kernel")->graph();
            }
        };
        //$edge->inject("kernel", $mock_kernel);
        $this->expectException("\Pho\Framework\Exceptions\InjectionUnavailableException");
        $edge->graph()->id();
    }

    public function testInjectableSuccess() {
        $mock_kernel = \Mockery::mock('StdClass');
        $mock_kernel->shouldReceive('graph')->andReturn($this->space);
        $actor = new Actor($this->space);
        $object = new Obj($actor, $this->space);
        $edge = new class($actor, $object) extends ActorOut\Write {
            public function graph() {
                return $this->injection("kernel")->graph();
            }
        };
        $edge->inject("kernel", $mock_kernel);
        $this->assertEquals($this->space->id(), $edge->graph()->id());
    }

    public function testCargoExport() {
        $actor = new Actor($this->space);
        $cargo = $actor->exportCargo();
        $this->assertArrayHasKey("in", $cargo);
        $this->assertInstanceOf(Cargo\IncomingEdgeCargo::class, $cargo["in"]);
        $this->assertArrayHasKey("out", $cargo);
        $this->assertInstanceOf(Cargo\OutgoingEdgeCargo::class, $cargo["out"]);
        $this->assertArrayHasKey("fields", $cargo);
        $this->assertInstanceOf(Cargo\FieldsCargo::class, $cargo["fields"]);
    }

}