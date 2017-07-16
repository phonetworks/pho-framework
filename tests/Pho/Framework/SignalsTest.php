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

}