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
    private $space;

    public function setUp() {
        $this->space = new Space();
    }

    public function tearDown() {
        unset($this->space);
    }

    public function testSpace() {
        $this->assertTrue($this->space->in($this->space));
    }

    public function testGraph() {
        $actor = new Actor($this->space);
        $graph = new Graph($actor, $this->space);
        $this->assertTrue($graph->in($this->space));
    }

    public function testSubGraph() {
        $actor = new Actor($this->space);
        $graph = new Graph($actor, $this->space);
        $subgraph = new Graph($actor, $graph);
        $this->assertTrue($subgraph->in($this->space));
        $this->assertTrue($subgraph->in($graph));
    }

    /*
    // !! SPACE CAN NO LONGER BE DISPARATE !!
    public function testDisparateSpaces() {
        $new_space = new Space();
        $actor = new Actor($this->space);
        $graph = new Graph($actor, $this->space);
        $subgraph = new Graph($actor, $graph);
        $this->assertFalse($this->space->in($new_space));
        $this->assertFalse($graph->in($new_space));
        $this->assertFalse($subgraph->in($new_space));
        $this->assertTrue($subgraph->in($this->space));
    }
    */

}