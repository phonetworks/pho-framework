<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */



namespace Pho\Framework {

    use Pho\Lib\Graph;

    class FormativeTest extends \PHPUnit\Framework\TestCase 
    {
        private $graph;

        public function setUp() {
            $this->graph = new Space();
        }

        public function tearDown() {
            unset($this->graph);
        }

        public function testSimpleFormation() {
            $actor = new Actor($this->graph);
            $actor->registerEdgeOutClass(\Pho\Framework\ActorOut\Post::class);
            $edge = $actor->post();
            $this->assertInstanceOf(Object::class, $edge->head()->node());
            $this->assertTrue($edge->predicate()->formative());
            $this->assertFalse($edge->predicate()->consumer());
        }

        public function testSimpleFormationWithConsumerPost() {
            $actor = new Actor($this->graph);
            $actor->registerEdgeOutClass(\Pho\Framework\ActorOut\ConsumerPost::class);
            $object = $actor->consumerpost();
            $this->assertInstanceOf(Object::class, $object);
            $actor_edges = $actor->edges()->out();
            $this->assertTrue($actor_edges->current()->predicate()->formative());
            $this->assertTrue($actor_edges->current()->predicate()->consumer());
        }        

        public function testFormationWithArgument() {
            $actor = new Actor($this->graph);
            $actor->registerEdgeOutClass(\Pho\Framework\ActorOut\Post::class);
            $text = "this is a good night story.";
            $edge = $actor->post($text);
            $this->assertInstanceOf(MockFable::class, $edge->head()->node());
            $this->assertEquals($text, $edge->head()->sayWhat());
        }

    }

    class MockFable extends Object {
        private $x;
        public function __construct(Actor $creator, ContextInterface $context, string $x) {
            parent::__construct($creator, $context);
            $this->x = $x;
        }
        public function sayWhat(): string
        {
            return $this->x;
        }
    }
};

namespace Pho\Framework\ActorOut {
    class Post extends Write {
        const HEAD_LABEL = "post";
        const HEAD_LABELS = "posts";
        const TAIL_LABEL = "poster";
        const TAIL_LABELS = "posters";
        const SETTABLES = [\Pho\Framework\Object::class, \Pho\Framework\MockFable::class]; 
    }
    class PostPredicate extends WritePredicate {
        const T_FORMATIVE = true;
        /*const FORMATION_PATTERNS = [
            ":" => \Pho\Framework\Object::class,
            "string" => \Pho\Framework\MockFable::class
        ];*/
    }
    class ConsumerPost extends Write {
        const HEAD_LABEL = "post";
        const HEAD_LABELS = "posts";
        const TAIL_LABEL = "poster";
        const TAIL_LABELS = "posters";
        const SETTABLES = [\Pho\Framework\Object::class, \Pho\Framework\MockFable::class]; 
    }
    class ConsumerPostPredicate extends WritePredicate {
        const T_FORMATIVE = true;
        const T_CONSUMER = true;
        /*const FORMATION_PATTERNS = [
            ":" => \Pho\Framework\Object::class,
            "string" => \Pho\Framework\MockFable::class
        ];*/
    }
};