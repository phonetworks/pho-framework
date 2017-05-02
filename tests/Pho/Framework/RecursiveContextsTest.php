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

class RecursiveContextsTest extends \PHPUnit\Framework\TestCase 
{
    private $graph;

    public function setUp() {
        $this->graph = new Graph();
    }

    public function tearDown() {
        unset($this->graph);
    }

    public function testGraph() {
        $this->assertTrue($this->graph->belongsOrEquals($this->graph));
    }

    public function testFrame() {
        $actor = new Actor($this->graph);
        $frame = new Frame($actor, $this->graph);
        $this->assertTrue($frame->belongsOrEquals($this->graph));
    }

    public function testSubFrame() {
        $actor = new Actor($this->graph);
        $frame = new Frame($actor, $this->graph);
        $subframe = new Frame($actor, $frame);
        $this->assertTrue($subframe->belongsOrEquals($this->graph));
        $this->assertTrue($subframe->belongsOrEquals($frame));
    }

    public function testDisparateGraphs() {
        $new_graph = new Graph();
        $actor = new Actor($this->graph);
        $frame = new Frame($actor, $this->graph);
        $subframe = new Frame($actor, $frame);
        $this->assertFalse($this->graph->belongsOrEquals($new_graph));
        $this->assertFalse($frame->belongsOrEquals($new_graph));
        $this->assertFalse($subframe->belongsOrEquals($new_graph));
        $this->assertTrue($subframe->belongsOrEquals($this->graph));
    }

}