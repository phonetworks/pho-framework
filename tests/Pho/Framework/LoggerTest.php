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

class LoggerTest extends \PHPUnit\Framework\TestCase 
{
    private $graph;

    public function setUp() {
        $this->graph = new Graph();
        \Pho\Lib\Graph\Logger::setVerbosity(2);
    }

    public function tearDown() {
        \Pho\Lib\Graph\Logger::setVerbosity(0);
        unset($this->graph);
    }

    public function testLogging() {
        ob_start();
        $node = new Actor($this->graph);
        $output = ob_get_clean();
        $expected = "A node with id \"";
        $this->assertEquals($expected, substr($output,0,strlen($expected)));
    }
}