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

class IDTest extends \PHPUnit\Framework\TestCase 
{
    private $space;

    public function setUp() {
        $this->space = new Space();
    }

    public function tearDown() {
        unset($this->space);
    }


    public function testActorEdge() {
        $actor = new Actor($this->space);
        $object = new Object($actor, $this->space);
        $edge = $actor->write($object);
        $subscription = $actor->subscribe($object);
        $this->assertEquals(4, (int) $actor->id()->toString()[0]);
        $this->assertEquals(5, (int) $object->id()->toString()[0]);
        $this->assertEquals(8, (int) $edge->id()->toString()[0]);
        $this->assertEquals(9, (int) $subscription->id()->toString()[0]);
    }


}