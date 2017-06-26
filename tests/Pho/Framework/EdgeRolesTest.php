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

class EdgeRolesTest extends \PHPUnit\Framework\TestCase 
{
    private $graph;

    public function setUp() {
        $this->graph = new Space();
    }

    public function tearDown() {
        unset($this->graph);
    }

    public function testDefaultEdgeRole() {
        $node1 = new Actor($this->graph);
        $node2 = new Object($node1, $this->graph);
        $edge = new class($node1, $node2) extends AbstractEdge {};
        $this->assertFalse($edge->predicate()->consumer());
        $this->assertFalse($edge->predicate()->notifier());
        $this->assertFalse($edge->predicate()->subscriber());
    }

    public function testRandomConsumer() {
        $new_predicate = new class() extends Predicate {
            const T_CONSUMER = true;
        };
        $node1 = new Actor($this->graph);
        $node2 = new Actor($this->graph);
        $edge = new class($node1, $node2, $new_predicate) extends AbstractEdge {};
        $this->assertTrue($edge->predicate()->consumer());
        $this->assertFalse($edge->predicate()->notifier());
        $this->assertFalse($edge->predicate()->subscriber());
    }

    public function testWriteEdge() {
        $actor = new Actor($this->graph);
        $object = new Object($actor, $this->graph);
        $edge = $actor->write($object);
        $this->assertFalse($edge->predicate()->consumer());
        $this->assertFalse($edge->predicate()->notifier());
        $this->assertTrue($edge->predicate()->subscriber());
        $this->assertTrue($edge->predicate()->binding());
    }

    public function testReadEdge() {
        $actor = new Actor($this->graph);
        $object = new Object($actor, $this->graph);
        $object_dup = $actor->read($object);
        $this->assertEquals($object->id(), $object_dup->id());
        $this->assertTrue($object_dup->edges()->in()->current()->predicate()->consumer());
        $this->assertFalse($object_dup->edges()->in()->current()->predicate()->notifier());
        $this->assertFalse($object_dup->edges()->in()->current()->predicate()->subscriber());
        $this->assertFalse($object_dup->edges()->in()->current()->predicate()->binding());
    }

    public function testSubscribeEdge() {
        $actor = new Actor($this->graph);
        $object = new Object($actor, $this->graph);
        $edge = $actor->subscribe($object);
        $this->assertFalse($edge->predicate()->consumer());
        $this->assertFalse($edge->predicate()->notifier());
        $this->assertTrue($edge->predicate()->subscriber());
        $this->assertFalse($edge->predicate()->binding());
    }
    
    public function testTransmitEdge() {
        $actor = new Actor($this->graph);
        $object = new Object($actor, $this->graph);
        $actor2 = new Actor($this->graph);
        $edge = $object->transmit($actor2);
        $this->assertFalse($edge->predicate()->consumer());
        $this->assertTrue($edge->predicate()->notifier());
        $this->assertFalse($edge->predicate()->subscriber());
        $this->assertFalse($edge->predicate()->binding());
    }

}