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

class SignalsTest extends \PHPUnit\Framework\TestCase 
{
    private $graph;

    public function setUp() {
        $this->graph = new Space();
    }

    public function tearDown() {
        unset($this->graph);
    }

    public function testNotificationSignal() {
        $ref1 = $ref2 = false;
        $actor = new Actor($this->graph);
        $friend = new Actor($this->graph);
        $friend->on("notification.received", function(AbstractNotification $notification) use (&$ref1) {
            $ref1 = true;
        });
        $actor->on("edge.created", function() use (&$ref2) {
            $ref2 = true;
        });
        $this->assertFalse($ref1);
        $this->assertFalse($ref2);
        $friend->subscribe($actor);
        $actor->write(new Object($actor, $this->graph));
        $this->assertTrue($ref2);
        $this->assertEquals(1, $friend->notifications()->count());
        $this->assertTrue($ref1);
    }

    public function testNodeAdded() {
        $ref = 0;
        $this->graph->on("node.added", function($node) use(&$ref) {
            $ref++;
        });
        $actor = new Actor($this->graph);
        $this->assertEquals(1, $ref);
    }

}