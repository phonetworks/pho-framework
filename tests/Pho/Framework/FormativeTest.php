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
            $actor->registerOutgoingEdges(\Pho\Framework\ActorOut\Post::class);
            $edge = $actor->post();
            $this->assertInstanceOf(Obj::class, $edge->head()->node());
            $this->assertTrue($edge->predicate()->formative());
            $this->assertFalse($edge->predicate()->consumer());
        }

        public function testSimpleFormationWithConsumerPost() {
            $actor = new Actor($this->graph);
            $actor->registerOutgoingEdges(\Pho\Framework\ActorOut\ConsumerPost::class);
            $object = $actor->consumerPost();
            $this->assertInstanceOf(Obj::class, $object);
            $actor_edges = $actor->edges()->out();
            $this->assertTrue($actor_edges->current()->predicate()->formative());
            $this->assertTrue($actor_edges->current()->predicate()->consumer());
        }        

        
        /*
        // this wouldn't work because Obj::class is abstract
        public function testSimpleFormationWithConsumerPostLongForm() {
            $actor = new Actor($this->graph);
            $actor->registerOutgoingEdges(\Pho\Framework\ActorOut\ConsumerPost::class);
            $object = $actor->consumerPostObj();
            $this->assertInstanceOf(Obj::class, $object);
        } 
        */

        public function testFormationWithArgument() {
            $actor = new Actor($this->graph);
            $actor->registerOutgoingEdges(\Pho\Framework\ActorOut\Post::class);
            $text = "this is a good night story.";
            $edge = $actor->post($text);
            $this->assertInstanceOf(MockFable::class, $edge->head()->node());
            $this->assertEquals($text, $edge->head()->sayWhat());
        }

        public function testFormationWithArgumentLongForm() {
            $actor = new Actor($this->graph);
            $actor->registerOutgoingEdges(\Pho\Framework\ActorOut\Post::class);
            $actor->registerOutgoingEdges(\Pho\Framework\ActorOut\ConsumerPost::class);
            $text = "this is a good night story.";
            $edge = $actor->postMockFable($text);
            $this->assertInstanceOf(MockFable::class, $edge->head()->node());
            $this->assertEquals($text, $edge->head()->sayWhat());
            $edge2 = $actor->consumerPostMockFable($text); 
            $this->assertInstanceOf(MockFable::class, $edge2); // because consumerPost is consumer
        }

    }

    class MockFable extends Obj {
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
        const FORMABLES = [\Pho\Framework\Obj::class, \Pho\Framework\MockFable::class]; 
    }
    class PostPredicate extends WritePredicate {
        const T_FORMATIVE = true;
    }
    class ConsumerPost extends Write {
        const HEAD_LABEL = "post";
        const HEAD_LABELS = "posts";
        const TAIL_LABEL = "poster";
        const TAIL_LABELS = "posters";
        const FORMABLES = [\Pho\Framework\Obj::class, \Pho\Framework\MockFable::class]; 
    }
    class ConsumerPostPredicate extends WritePredicate {
        const T_FORMATIVE = true;
        const T_CONSUMER = true;
    }
};