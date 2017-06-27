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

class NotificationTest extends \PHPUnit\Framework\TestCase 
{
    private $graph;

    public function setUp() {
        $this->graph = new Space();
    }

    public function tearDown() {
        unset($this->graph);
    }

    public function testSubscriptionNotifications() {
        $actor = new Actor($this->graph);
        $friend = new Actor($this->graph);
        $friend->subscribe($actor);
        $obj = new Object($actor, $this->graph);
        $edge = $actor->write($obj);
        $this->assertEquals(0, $actor->notifications()->count());
        $this->assertEquals(1, $friend->notifications()->count());
        $this->assertCount(1, $friend->notifications()->toArray());
        $this->assertEquals($edge->id()->toString(), $friend->notifications()->toArray()[0]["edge"]);
    }


    public function testMentionNotifications() {
        $actor = new Actor($this->graph);
        $friend = new Actor($this->graph);
        $obj = new Object($actor, $this->graph);
        $edge = $obj->mention($friend);
        $this->assertEquals(0, $actor->notifications()->count());
        $this->assertEquals(1, $friend->notifications()->count());
        $this->assertCount(1, $friend->notifications()->toArray());
        $this->assertEquals($edge->id()->toString(), $friend->notifications()->toArray()[0]["edge"]);
    }


    public function testMultiSubscriptionNotifications() {
        $actor = new Actor($this->graph);
        $friend1 = new Actor($this->graph);
        $friend2 = new Actor($this->graph);
        $friend1->subscribe($actor);
        $friend2->subscribe($actor);
        $obj = new Object($actor, $this->graph);
        $edge = $actor->write($obj);
        $this->assertEquals(0, $actor->notifications()->count());
        $this->assertEquals(1, $friend1->notifications()->count());
        $this->assertEquals(1, $friend2->notifications()->count());
        $this->assertEquals($edge->id()->toString(), $friend1->notifications()->toArray()[0]["edge"]);
        $this->assertEquals($edge->id()->toString(), $friend2->notifications()->toArray()[0]["edge"]);
    }

    public function testMultiMentionNotifications() {
        $actor = new Actor($this->graph);
        $friend1 = new Actor($this->graph);
        $friend2 = new Actor($this->graph);
        $obj = new Object($actor, $this->graph);
        $edge1 = $obj->mention($friend1);
        $edge2 = $obj->mention($friend2);
        $this->assertEquals(0, $actor->notifications()->count());
        $this->assertEquals(1, $friend1->notifications()->count());
        $this->assertCount(1, $friend2->notifications()->toArray());
        $this->assertEquals($edge1->id()->toString(), $friend1->notifications()->toArray()[0]["edge"]);
        $this->assertEquals($edge2->id()->toString(), $friend2->notifications()->toArray()[0]["edge"]);
    }

}